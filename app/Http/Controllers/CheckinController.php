<?php

namespace App\Http\Controllers;

use App\Delegate;
use App\Enums\DelegateDuplicationStatus;
use App\Event;
use App\Http\Resources\CheckinTransactionResource;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckinController extends Controller
{
    public function search(Event $event) {

        $result = [];

        if ($keyword = request('keyword')) {

            $result = Delegate::whereEventId($event->id)
                              ->with('transactions.ticket')
                              ->where('is_duplicated', '<>',
                                  DelegateDuplicationStatus::DUPLICATED)
                              ->where(function ($query) use ($event, $keyword) {
                                  $registrationId = (int)str_replace(setting($event,
                                      'registration_id_prefix'), "", $keyword);

                                  $registrationId = $registrationId > 0 ? $registrationId : $keyword;
                                  $query->where('registration_id', 'like',
                                      "%{$registrationId}%")
                                        ->orWhere('email', 'like',
                                            "%{$keyword}%")
                                        ->orWhere('mobile', 'like',
                                            "%{$keyword}%")
                                        ->orWhere('first_name', 'like',
                                            "%{$keyword}%")
                                        ->orWhere('last_name', 'like',
                                            "%{$keyword}%");
                              })
                              ->get()
                              ->map(function (Delegate $delegate) {
                                  return [
                                      'prefix'          => $delegate->prefix,
                                      'first_name'      => $delegate->first_name,
                                      'last_name'       => $delegate->last_name,
                                      'transactions'    => $delegate->transactions,
                                      'registration_id' => $delegate->getRegistrationId(),
                                  ];
                              });
        }

        return response()->json($result);

    }

    public function index(Event $event) {
        return view("admin.events.checkin.index", compact('event'));
    }

    public function simpleIndex(Event $event) {
        return view("admin.events.checkin.index_simple", compact('event'));
    }

    public function checkIn(
        Event $event, string $token, Request $request, Transaction $transaction
    ) {
        $data = $transaction->parseUuid($token);

        $transaction = $transaction->find($data['transaction_id']);

        $delegate = Delegate::find($data['delegate_id']);

        if ($transaction->payee->isNot($delegate) or
            $delegate->event->isNot($event) or
            $transaction->ticket->event->isNot($event)) {
            abort(403);
        }

        $transaction->checkIn($request->user());

        $record = DB::table('check_in')
                    ->latest()
                    ->where('transaction_id', $transaction->id)
                    ->first();

        return response()->json([
            'status' => 'completed',
            'record' => [
                'timestamp' => $record->created_at,
                'user'      => User::find($record->user_id),
            ]
        ]);
    }

    public function getDelegate(
        Event $event, string $token, Request $request, Transaction $transaction
    ) {
        $data = $transaction->parseUuid($token);
        $transaction = $transaction->find($data['transaction_id']);

        $delegate = \App\Delegate::find($data['delegate_id']);

        if ($transaction->payee->isNot($delegate) or
            $delegate->event->isNot($event) or
            $transaction->ticket->event->isNot($event)
        ) {
            abort(403);
        }

        return new CheckinTransactionResource($transaction);

    }
}
