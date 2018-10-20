<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentServiceInterface;
use App\Delegate;
use App\DelegateRole;
use App\Enums\SystemEvents;
use App\Enums\TransactionStatus;
use App\Event;
use App\Events\SystemEvent;
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

    public function pay(Request $request, PaymentServiceInterface $service) {

        $validatedData = $this->validate($request,
            array_merge($this->delegate->getStoreRules(), [
                'token'     => 'required',
                'ticket_id' => 'required',
            ]));

        $response = redirect()->back();
        $message = null;

        if ($ticketID = $validatedData['ticket_id'] and $ticket = \App\Ticket::find($ticketID)) {
            try {

                $chargeResponse = $service->charge($request->get('token'),
                    $ticket->price);

                DB::beginTransaction();

                try {
                    $event = Event::first();

                    $newDelegate = $this->createDelegate($event, $validatedData,
                        $chargeResponse, $ticket);

                    DB::commit();

                    event(new SystemEvent(SystemEvents::CREATE_DELEGATE,
                        $newDelegate));

                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }

                $message = "Buy the ticket.";

            } catch (\Exception $e) {
                $response = $response->withInput();
                $message = $e->getMessage();
            }

            return $message = null ? $response : $response->withAlert($message);
        }

        return $response->withAlert('Ticket is invalid!');
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
}
