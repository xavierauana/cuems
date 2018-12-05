<?php

namespace App\Http\Controllers;

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
use App\Services\DelegateDuplicateChecker;
use App\Services\JETCOPaymentService;
use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                'ticket_id' => 'required|in:' . implode(",", Ticket::public()
                                                                   ->available()
                                                                   ->pluck('id')
                                                                   ->toArray()),
            ]));

        $validatedData = $this->sanitizeInputData($validatedData);

        if ($service->checkPaymentGatewayStatus()) {

            if (!$prefix = env('JETCO_PREFIX', null)) {
                throw new \Exception("JETCO PREFIX setting error.");
            }

            $invoiceId = "test_" . str_random(5);

            $invoiceNumber = $prefix . $invoiceId;

            $record = PaymentRecord::updateOrCreate([
                'invoice_id' => $invoiceNumber
            ], [
                'status'    => PaymentRecordStatus::CREATED,
                'form_data' => json_encode($validatedData)
            ]);

            $DORequest = new DigitalOrderRequest(
                $invoiceNumber,
                100,
                PaymentType::Authorisation,
                route("paymentCallBack", ['ref_id' => $record->id])
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

    public function paid(Request $request, JETCOPaymentService $service) {

        $response = simplexml_load_string($service->checkPaymentStatus(["DR" => $request->get('String1')]));

        if ((string)$response->Status === "AP") {

            $record = PaymentRecord::findOrFail($request->get('ref_id'));
            $formData = json_decode($record->form_data, true);

            $ticket = Ticket::findOrFail($formData['ticket_id']);

            $chargeResponse = $service->charge((string)$response->InvoiceNo,
                $ticket->price);

            DB::beginTransaction();

            try {
                $event = Event::first();

                $newDelegate = $this->createDelegate($event, $formData,
                    $chargeResponse, $ticket);

                $this->checkIsNewDelegateIsDuplicated($event, $newDelegate);

                DB::commit();

                $record->update(['status' => PaymentRecordStatus::AUTHORIZED]);

                event(new SystemEvent(SystemEvents::CREATE_DELEGATE,
                    $newDelegate));

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            return redirect("/")->withAlert("Thank you. You payment have been confirmed.");
        }


        return redirect("/")->withAlert("Something wrong. Please try again.");

    }

    /**
     * @param $event
     * @param $validatedData
     * @param $chargeResponse
     * @param $ticket
     * @return mixed
     */
    private function createDelegate(
        $event, $validatedData, $chargeResponse, $ticket
    ) {

        $newDelegate = $event->delegates()->create($validatedData);

        $defaultRole = DelegateRole::whereIsDefault(true)
                                   ->firstOrFail();

        $newDelegate->roles()->save($defaultRole);

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

        $checker = new DelegateDuplicateChecker($event);

        $duplications = $checker->find(['email', 'mobile'],
            [$newDelegate->email, $newDelegate->mobile]);

        $newDelegate->is_duplicated = ($duplications->count() > 1) ?
            DelegateDuplicationStatus::DUPLICATED :
            $newDelegate->is_duplicated = DelegateDuplicationStatus::NO;

        $newDelegate->save();
    }
}
