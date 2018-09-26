<?php

namespace App\Http\Controllers;

use App\Event;
use App\Jobs\ImportTickets;
use App\Ticket;
use Illuminate\Http\Request;

class TicketsController extends Controller
{
    /**
     * @var \App\Ticket
     */
    private $repo;

    /**
     * TicketsController constructor.
     * @param \App\Ticket $repo
     */
    public function __construct(Ticket $repo) {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Event $event
     * @return void
     */
    public function index(Event $event) {
        $event->load('tickets');

        return view('admin.events.tickets.index', compact('event'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \App\Event $event
     * @return \Illuminate\Http\Response
     */
    public function create(Event $event) {
        return view('admin.events.tickets.create', compact('event'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \App\Event                $event
     * @return void
     */
    public function store(Request $request, Event $event) {
        $validatedData = $this->validate($request, Ticket::StoreRules,
            Ticket::ErrorMessages);

        $event->tickets()->create($validatedData);

        return redirect()->route('events.tickets.index', $event)
                         ->withStatus("New ticket created!");

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Ticket $ticket
     * @return \Illuminate\Http\Response
     */
    public function show(Ticket $ticket) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Ticket $ticket
     * @return \Illuminate\Http\Response
     */
    public function edit(Ticket $ticket) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Ticket              $ticket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ticket $ticket) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Ticket $ticket
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ticket $ticket) {
        //
    }

    public function getImport(Event $event) {
        return view('admin.events.tickets.import', compact('event'));
    }

    public function postImport(Event $event, Request $request) {

        $this->validate($request, [
            //            'file' => 'required|file|min:0|mimes:csv'
            'file' => 'required|file|min:0'
        ]);

        $file = $request->file('file');

        $filePath = $file->move("../storage/temp",
            $file->getClientOriginalName());

        $job = new ImportTickets($event, $filePath);

        $this->dispatch($job);

        return redirect()->route('events.tickets.index', $event);
    }
}
