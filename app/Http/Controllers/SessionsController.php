<?php

namespace App\Http\Controllers;

use App\Event;
use App\Session;
use Illuminate\Http\Request;

class SessionsController extends Controller
{
    /**
     * @var Session
     */
    private $repo;

    /**
     * SessionsController constructor.
     */
    public function __construct(Session $repo) {
        $this->repo = $repo;
    }


    /**
     * Display a listing of the resource.
     *
     * @param Event $event
     * @return void
     */
    public function index(Event $event) {

        $event->load('sessions.talks');

        return view('admin.events.sessions.index', compact("event"));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Event $event
     * @return \Illuminate\Http\Response
     */
    public function create(Event $event) {
        return view('admin.events.sessions.create', compact('event'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Event   $event
     * @return void
     */
    public function store(Request $request, Event $event) {

        $validatedData = $this->validate($request, Session::StoreRules,
            Session::ErrorMessages);

        $event->sessions()->create($validatedData);

        return redirect()->route("events.sessions.index", $event)
                         ->withStatu('New session created!');
    }

    /**
     * Display the specified resource.
     *
     * @param Event    $event
     * @param  Session $session
     * @return void
     */
    public function show(Event $event, Session $session) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Event    $event
     * @param  Session $session
     * @return void
     */
    public function edit(Event $event, Session $session) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  Session $session
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Session $session) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Session $session
     * @return \Illuminate\Http\Response
     */
    public function destroy(Session $session) {
        //
    }
}
