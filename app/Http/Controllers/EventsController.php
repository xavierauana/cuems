<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    private $repo;

    /**
     * EventController constructor.
     * @param \App\Event $event
     */
    public function __construct(Event $event) {

        $this->repo = $event;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $events = $this->repo->get();

        return view("admin.events.index", compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        return view("admin.events.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse {

        $this->validate($request, Event::StoreRules, Event::ValidationMessages);

        $this->repo->create($request->all());

        return redirect()->route("events.index");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Event $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Event $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Event               $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Event $event
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Event $event) {

        $event->delete();

        return response()->json(['status' => "completed"]);
    }

    public function details(Event $event) {
        return view('admin.events.details.index', compact('event'));
    }
}
