<?php

namespace App\Http\Controllers;

use App\Event;
use App\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Event $event
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Event $event) {

        $transactions = Transaction::forEvent($event)
                                   ->with(['payee', 'ticket'])
                                   ->search($request->query('keyword'))
                                   ->orderBy($request->get('orderBy',
                                       'd.registration_id'),
                                       $request->get('order',
                                           'desc'))
                                   ->with('payee.event')
                                   ->paginate();

        return view('admin.events.transactions.index',
            compact('transactions', 'event'));
    }

    public function search(
        Request $request, Event $event, Transaction $transaction
    ) {

        if (!($keyword = $request->query('keyword'))) {
            return redirect()->route('events.transactions.index', $event);
        }

        $transactions = $transaction->forEvent($event)
                                    ->with('ticket', 'payee')
                                    ->search($keyword, [$event])
                                    ->orderBy($request->query('orderBy',
                                        'd.registration_id'),
                                        $request->query('order', 'desc'))
                                    ->paginate();

        return view("admin.events.transactions.index",
            compact('event', 'transactions'));

    }
}
