<?php
/**
 * Author: Xavier Au
 * Date: 2/12/2018
 * Time: 1:10 PM
 */

use App\Delegate;
use App\Event;
use App\Http\Resources\CheckinTransactionResource;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


Route::get('events/{event}/checkin/{token}/delegate',
    function (
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

    })->name('events.checkin.getDelegate');

Route::post('events/{event}/checkin/{token}',
    function (
        Event $event, string $token, Request $request, Transaction $transaction
    ) {

        $data = $transaction->parseUuid($token);

        $transaction = $transaction->find($data['transaction_id']);

        $delegate = \App\Delegate::find($data['delegate_id']);

        if ($transaction->payee->isNot($delegate) or
            $delegate->event->isNot($event) or
            $transaction->ticket->event->isNot($event)) {
            abort(403);
        }

        $transaction->checkIn($request->user());

        $record = DB::table('check_in')->latest()
                    ->where('transaction_id', $transaction->id)->first();

        return response()->json([
            'status' => 'completed',
            'record' => [
                'timestamp' => $record->created_at,
                'user'      => User::find($record->user_id),
            ]
        ]);

    })->name('events.checkin.delegate');

Route::get('/test_checkin', function () {
    $transaction = Event::find(2)->delegates()->get()->random()->first()
                        ->transactions()->first();

    return \QrCode::size(150)->generate($transaction->uuid);
});


Route::get('events/{event}/checkin/search',
    function (Event $event) {

        $result = [];
        if ($keyword = request('keyword')) {
            $result = Delegate::whereEventId($event->id)
                              ->with('transactions.ticket')
                              ->where(function ($query) use ($event, $keyword) {
                                  $registrationId = (int)str_replace(setting($event,
                                      'registration_id_prefix'), "", $keyword);
                                  $query->where('registration_id', 'like',
                                      "%{$registrationId}%")
                                        ->orWhere('email', 'like',
                                            "%{$keyword}%")
                                        ->orWhere('mobile', 'like',
                                            "%{$keyword}%");
                              })
                              ->get();
        }

        return response()->json($result);

    })->name('events.checkin.index');


Route::get('events/{event}/checkin',
    function (Event $event) {

        return view("admin.events.checkin.index", compact('event'));

    })->name('events.checkin.index');
