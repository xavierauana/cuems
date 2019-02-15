<?php

namespace App\Http\Controllers;

use App\Event;
use App\Imports\TicketImport;
use App\Jobs\ImportTickets;
use App\Ticket;
use Illuminate\Http\Request;

class TicketsController extends Controller
{
    /**
     * @var Ticket
     */
    private $repo;

    /**
     * TicketsController constructor.
     * @param Ticket $repo
     */
    public function __construct(Ticket $repo) {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Event $event
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
     * @param  Request   $request
     * @param \App\Event $event
     * @return
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, Event $event) {
        $validatedData = $this->validate($request,
            $this->repo->getStoreRules($event->id),
            Ticket::ErrorMessages);

        $validatedData['is_public'] = isset($validatedData['is_public']);

        $event->tickets()->create($validatedData);

        return redirect()->route('events.tickets.index', $event)
                         ->withStatus("New ticket created!");

    }

    /**
     * Display the specified resource.
     *
     * @param  Ticket $ticket
     * @return void
     */
    public function show(Ticket $ticket) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Event $event
     * @param  Ticket    $ticket
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event, Ticket $ticket) {

        return view("admin.events.tickets.edit", compact('ticket', 'event'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request   $request
     * @param \App\Event $event
     * @param  Ticket    $ticket
     * @return
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Event $event, Ticket $ticket) {
        $validatedData = $this->validate($request,
            $ticket->getUpdateRules($event->id),
            Ticket::ErrorMessages);

        $validatedData['is_public'] = isset($validatedData['is_public']);

        $ticket->update($validatedData);

        return redirect()->route("events.tickets.index", $event)
                         ->withStatus("Ticket: {$ticket->name} is updated!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Event $event
     * @param  Ticket    $ticket
     * @return
     */
    public function destroy(Event $event, Ticket $ticket) {
        $event->tickets()->whereId($ticket->id)->delete();

        return redirect()->back()
                         ->withStatus("Ticket {$ticket->name} is deleted!");
    }

    public function getImport(Event $event) {
        return view('admin.events.tickets.import', compact('event'));
    }

    /**
     * @param \App\Event $event
     * @param Request    $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function postImport(Event $event, Request $request) {

        $this->validate($request, [
            'file' => 'required|file|min:0.1'
        ]);

        $job = new ImportTickets($event, $request->file('file'));

        $this->dispatch($job);

        return redirect()->route('events.tickets.index', $event);
    }

    public function template() {
        return response()->download(storage_path('app/templates/tickets_template.csv'));
    }
}
