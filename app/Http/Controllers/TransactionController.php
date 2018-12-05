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
    public function index(Event $event) {

        $query = Transaction::whereIn('ticket_id',
            function ($query) use ($event) {
                $query->select('id')
                      ->from("tickets")
                      ->where('event_id', $event->id);
            })->with(['payee', 'ticket'])->latest();


        $transactions = $query->paginate();

        return view('admin.events.transactions.index',
            compact('transactions', 'event'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Transaction         $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction) {
        //
    }

    public function search(
        Request $request, Event $event, Transaction $transaction
    ) {
        if ($request->has('keyword')) {

            $transactions = $transaction
                ->join('delegates as d',
                    'transactions.payee_id', '=', 'd.id')
                ->join('tickets as t',
                    'transactions.ticket_id', '=', 't.id')
                ->where('transactions.charge_id', 'like',
                    "%" . $request->get('keyword') . "%")
                ->orWhere('d.first_name', 'like',
                    "%" . $request->get('keyword') . "%")
                ->orWhere('d.last_name', 'like',
                    "%" . $request->get('keyword') . "%")
                ->orWhere('t.name', 'like',
                    "%" . $request->get('keyword') . "%")
                ->paginate();

            return view("admin.events.transactions.index",
                compact('event', 'transactions'));

        }

        return redirect()->route('events.transactions.index', $event);
    }
}
