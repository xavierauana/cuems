<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentServiceInterface;
use App\Delegate;
use App\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function pay(Request $request, PaymentServiceInterface $service) {


        $validatedData = $this->validate($request,
            array_merge(Delegate::StoreRules, [
                'token'  => 'required',
                'ticket' => 'required',
            ]));

        $response = redirect()->back();
        $message = null;

        if ($ticketID = $request->get('ticket') and $ticket = \App\Ticket::find($ticketID)) {
            try {

                $chargeResponse = $service->charge($request->get('token'),
                    $ticket->price);

                DB::beginTransaction();

                try {
                    $event = Event::first();

                    $newDelegate = $event->delegates()->create($validatedData);

                    $newDelegate->transactions()->create([
                        'charge_id'  => $chargeResponse->chargeID,
                        'card_brand' => $chargeResponse->brand,
                        'last_4'     => $chargeResponse->last4,
                        'ticket_id'  => $ticket->id,
                        'status'     => "completed",
                    ]);

                    DB::commit();

                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }

                $message = "Buy the ticket.";

            } catch (Exception $e) {
                $response = $response->withInput();
                $message = $e->getMessage();
            }

            return $message = null ? $response : $response->withAlert($message);
        }

        return $response->withAlert('Ticket is invalid!');
    }
}
