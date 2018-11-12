<?php

namespace App\Http\Controllers;

use App\Delegate;
use App\DelegateRole;
use App\Entities\DigitalOrderRequest;
use App\Enums\PaymentRecordStatus;
use App\Enums\PaymentType;
use App\Enums\TransactionStatus;
use App\PaymentRecord;
use App\Services\JETCOPaymentService;
use App\Ticket;
use Illuminate\Http\Request;

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

    public function pay(Request $request, JETCOPaymentService $service) {

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
            'status'     => TransactionStatus::COMPLETED,
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
}
