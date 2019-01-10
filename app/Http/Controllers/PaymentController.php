<?php

namespace App\Http\Controllers;

use App\Contracts\DuplicateCheckerInterface;
use App\Delegate;
use App\DelegateRole;
use App\Entities\DigitalOrderRequest;
use App\Enums\DelegateDuplicationStatus;
use App\Enums\PaymentRecordStatus;
use App\Enums\PaymentType;
use App\Enums\SystemEvents;
use App\Enums\TransactionStatus;
use App\Event;
use App\Events\SystemEvent;
use App\PaymentRecord;
use App\Services\DelegateCreationService;
use App\Services\JETCOPaymentService;
use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    /**
     * @var \App\Delegate
     */
    private $delegate;

    /**
     * PaymentController constructor.
     * @param \App\Delegate $delegate
     */
    public function __construct(Delegate $delegate) {
        $this->delegate = $delegate;
    }

    public function token(Request $request, JETCOPaymentService $service) {

        $validatedData = $this->validate($request,
            array_merge($this->delegate->getStoreRules(), [
                'ticket_id' => [
                    'required',
                    Rule::exists('tickets', 'id')->where(function ($query) {
                        $query->where('is_public', true);
                    })
                ],
                [
                    'event_id' => 'required|exists:events,id'
                ]
            ]));

        $validatedData = $this->sanitizeInputData($validatedData);

        if ($service->checkPaymentGatewayStatus()) {

            if (!$prefix = config('event.payment_prefix', null)) {
                throw new \Exception("JETCO PREFIX setting error.");
            }

            $invoiceId = config('event.invoice_prefix') . str_random(5);

            $invoiceNumber = $prefix . $invoiceId;

            $record = PaymentRecord::updateOrCreate([
                'invoice_id' => $invoiceNumber,
                'event_id'   => $request->get('event')
            ], [
                'status'    => PaymentRecordStatus::CREATED,
                'form_data' => json_encode($validatedData),
            ]);

            $ticket = Ticket::findOrFail($validatedData['ticket_id']);

            $DORequest = new DigitalOrderRequest(
                $invoiceNumber,
                $ticket->price,
                PaymentType::Authorisation,
                route("paymentCallBack",
                    [
                        'ref_id' => $record->id,
                        'event'  => $request->get('event')
                    ])
            );

            $data = $service->getDigitalOrder($DORequest);

            PaymentRecord::updateOrCreate([
                'invoice_id' => $invoiceNumber,
            ], [
                'status' => PaymentRecordStatus::REQUEST,
            ]);

            return response()->json($data);

        }
    }

    public function paid(
        Request $request, JETCOPaymentService $service,
        DelegateCreationService $creationService
    ) {

        $response = simplexml_load_string($service->checkPaymentStatus(["DR" => $request->get('String1')]));

        $record = PaymentRecord::findOrFail($request->get('ref_id'));

        $event = $record->event;

        if ((string)$response->Status === "AP") {

            $formData = json_decode($record->form_data, true);

            $ticket = Ticket::findOrFail($formData['ticket_id']);

            $chargeResponse = $service->charge((string)$response->InvoiceNo,
                $ticket->price);

            $newDelegate = $creationService->selfCreate($event, $formData,
                $chargeResponse, $request->get('ref_id'));

            event(new SystemEvent(SystemEvents::CREATE_DELEGATE,
                $newDelegate));

            //            DB::beginTransaction();
            //
            //            try {
            //
            //                $newDelegate = $this->createDelegate($event, $formData,
            //                    $chargeResponse, $ticket);
            //
            //                $this->checkIsNewDelegateIsDuplicated($event, $newDelegate);
            //
            //                $record->update(['status' => PaymentRecordStatus::AUTHORIZED]);
            //
            //                DB::commit();
            //
            //
            //                event(new SystemEvent(SystemEvents::CREATE_DELEGATE,
            //                    $newDelegate));
            //
            //            } catch (\Exception $e) {
            //                DB::rollBack();
            //                throw $e;
            //            }

            return redirect(url("/",
                ['event' => $event->id]))->withAlert("Thank you. You payment have been confirmed.");
        }


        return redirect(url("/",
            ['event' => $event->id]))->withAlert("Something wrong. Please try again.");

    }

    /**
     * @param $event
     * @param $validatedData
     * @param $chargeResponse
     * @param $ticket
     * @return mixed
     */
    private function createDelegate(
        Event $event, $validatedData, $chargeResponse, $ticket
    ) {

        $validatedData['registration_id'] = ($event->delegates()
                                                   ->max('registration_id') ?? 0) + 1;

        /** @var \App\Delegate $newDelegate */
        $newDelegate = $event->delegates()->create($validatedData);

        $newDelegate->roles()->save(DelegateRole::whereIsDefault(true)
                                                ->firstOrFail());

        $newDelegate->transactions()->create([
            'charge_id'  => $chargeResponse->chargeID,
            'card_brand' => $chargeResponse->brand,
            'last_4'     => $chargeResponse->last4,
            'ticket_id'  => $ticket->id,
            'status'     => TransactionStatus::AUTHORIZED,
        ]);

        return $newDelegate;
    }

    private function sanitizeInputData($validatedData): array {

        $validatedData['institution'] = $this->notEmptyAndOther($validatedData,
            'institution',
            'other_institution') ? $validatedData['other_institution'] : $validatedData['institution'];

        if (isset($validatedData['training_organisation'])) {
            $validatedData['training_organisation'] = $this->notEmptyAndOther($validatedData,
                'training_organisation',
                'training_other_organisation') ? $validatedData['training_other_organisation'] : $validatedData['training_organisation'];
        }

        return $validatedData;
    }

    /**
     * @param array  $array
     * @param string $key
     * @param string $key2
     * @return bool
     */
    private function notEmptyAndOther(array $array, string $key, string $key2
    ): bool {

        return $array[$key] == 'other' and !empty($array[$key2]);
    }

    /**
     * @param $event
     * @param $newDelegate
     */
    private function checkIsNewDelegateIsDuplicated($event, $newDelegate
    ): void {

        $checker = app(DuplicateCheckerInterface::class)->setEvent($event);

        $emailDuplications = $checker->isDuplicated('email',
            $newDelegate->email);
        $mobileDuplications = $checker->isDuplicated('mobile',
            $newDelegate->mobile);

        $newDelegate->is_duplicated = ($emailDuplications or $mobileDuplications) ?
            DelegateDuplicationStatus::DUPLICATED :
            $newDelegate->is_duplicated = DelegateDuplicationStatus::NO;

        $newDelegate->save();
    }
}
