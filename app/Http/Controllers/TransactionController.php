<?php

namespace App\Http\Controllers;

use App\Delegate;
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

        $query = Transaction::whereIn('transactions.ticket_id',
            function ($query) use ($event) {
                $query->select('id')
                      ->from("tickets")
                      ->where('event_id', $event->id);
            })->with(['payee', 'ticket']);

        $queries = $request->query();
        $query = $this->joinTables($query);
        $query = $this->orderQuery($queries, $query);
        $query = $this->searchQuery($request->query('keyword'), $query);

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
     * @return void
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
        if ($keyword = $request->query('keyword')) {

            $query = $this->joinTables($transaction);
            $query = $this->orderQuery($request->query(), $query);
            $query = $this->searchQuery($keyword, $query);

            $transactions = $query->paginate();

            return view("admin.events.transactions.index",
                compact('event', 'transactions'));

        }

        return redirect()->route('events.transactions.index', $event);
    }

    /**
     * @param $queries
     * @param $query
     * @return mixed
     */
    private function orderQuery($queries, $query) {

        if (in_array('first_name', array_keys($queries))) {
            $query = $query->orderBy('d.first_name', $queries['first_name']);

            unset($queries['first_name']);
        }

        if (in_array('registration_id', array_keys($queries))) {
            $query = $query->orderBy('d.registration_id',
                $queries['registration_id']);

            unset($queries['registration_id']);
        }

        if (in_array('ticket', array_keys($queries))) {
            $query = $query->orderBy('t.name', $queries['ticket']);

            unset($queries['ticket']);
        }

        if (in_array('keyword', array_keys($queries))) {
            unset($queries['keyword']);
        }
        foreach ($queries as $key => $oder) {
            $query = $query->orderBy('transactions.' . $key, $oder);
        }

        return $query;
    }

    private function searchQuery(string $keyword = null, $query) {

        if (is_null($keyword)) {
            return $query;
        }
        $columns = [
            'transactions.status',
            'transactions.charge_id',
            'd.registration_id',
            'd.first_name',
            'd.last_name',
            'd.email',
            't.name',
        ];

        $query->where(function ($q) use ($columns, $keyword
        ) {
            foreach ($columns as $index => $column) {
                if ($index === 0) {
                    $q->where($column, "like",
                        "%{$keyword}%");
                } else {
                    $q->orWhere($column, "like",
                        "%{$keyword}%");
                }
            }
        });

        return $query;
    }

    private function joinTables($query) {
        $query = $query->join('delegates as d', 'transactions.payee_id', '=',
            'd.id')
                       ->where('transactions.payee_type', Delegate::class)
                       ->join('tickets as t', 'transactions.ticket_id', '=',
                           't.id');


        return $query;
    }
}
