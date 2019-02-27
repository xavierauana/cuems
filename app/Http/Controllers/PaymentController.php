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
use Illuminate\Support\Facades\Log;
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

            $delegateCount = Event::find(request('event'))
                                  ->delegates()->count();
            $invoiceId = config('event.invoice_prefix') . $delegateCount . str_random(5);

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
                        'ref_id' => base64_encode($record->id)
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

    /**
     * @param \Illuminate\Http\Request              $request
     * @param \App\Services\JETCOPaymentService     $service
     * @param \App\Services\DelegateCreationService $creationService
     * @return mixed
     * @throws \Exception
     */
    public function paid(
        Request $request, JETCOPaymentService $service,
        DelegateCreationService $creationService
    ) {

        $response = simplexml_load_string($service->checkPaymentStatus(["DR" => $request->get('String1')]));

        $record = PaymentRecord::findOrFail(base64_decode($request->get('ref_id')));

        $event = $record->event;

        if ((string)$response->Status === "AP") {

            $formData = json_decode($record->form_data, true);

            $ticket = Ticket::findOrFail($formData['ticket_id']);

            $chargeResponse = $service->charge((string)$response->InvoiceNo,
                $ticket->price);

            $formData['institution'] = $formData['other_institution'] ?? $formData['institution'];
            $formData['training_organisation'] = $formData['training_other_organisation'] ?? ($formData['training_organisation']??null);

            $newDelegate = $creationService->selfCreate($event, $formData,
                $chargeResponse, $record);

            Log::info('Going to send the event');

            event(new SystemEvent(SystemEvents::CREATE_DELEGATE,
                $newDelegate));

            return redirect(url("/?event=" . $event->id))->withAlert(setting($event,
                'payment_successful_msg'));
        }

        $record->update([
            'status' => 'failed'
        ]);

        return redirect(url("/?event=" . $event->id))->withAlert(setting($event,
            'payment_failed_msg'));

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

        $validatedData['institution'] = $this->notEmptyOrOther($validatedData,
            'institution',
            'other_institution') ? $validatedData['other_institution'] : $validatedData['institution'];

        $validatedData['position'] = $this->notEmptyOrOther($validatedData,
            'position',
            'other_position') ? $validatedData['other_position'] : $validatedData['position'];

        if (isset($validatedData['training_organisation'])) {
            $validatedData['training_organisation'] = $this->notEmptyOrOther($validatedData,
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
    private function notEmptyOrOther(array $array, string $key, string $key2
    ): bool {

        return ($array[$key] == 'other' or $array[$key] == 'Others') and !empty($array[$key2]);
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
