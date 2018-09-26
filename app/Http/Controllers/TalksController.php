<?php

namespace App\Http\Controllers;

use App\Event;
use App\Session;
use App\Talk;
use Illuminate\Http\Request;

class TalksController extends Controller
{

    private $repo;

    /**
     * TalksController constructor.
     * @param \App\Talk $talk
     */
    public function __construct(Talk $talk) {
        $this->repo = $talk;
    }


    /**
     * Display a listing of the resource.
     *
     * @param \App\Event   $event
     * @param \App\Session $session
     * @return void
     */
    public function index(Event $event, Session $session) {
        $session->load('talks');

        return view('admin.events.sessions.talks.index',
            compact('event', 'session'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \App\Event   $event
     * @param \App\Session $session
     * @return void
     */
    public function create(Event $event, Session $session) {
        return view('admin.events.sessions.talks.create',
            compact('event', 'session'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \App\Event                $event
     * @param \App\Session              $session
     * @return void
     */
    public function store(Request $request, Event $event, Session $session) {
        $validatedData = $this->validate($request, Talk::StoreRules,
            Talk::ErrorMessages);

        $session->talks()->create($validatedData);

        return redirect()->route('events.sessions.talks.index',
            [$event, $session])->withStatus('New talk created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Talk $talk
     * @return \Illuminate\Http\Response
     */
    public function show(Talk $talk) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Talk $talk
     * @return \Illuminate\Http\Response
     */
    public function edit(Talk $talk) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Talk                $talk
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Talk $talk) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Talk $talk
     * @return \Illuminate\Http\Response
     */
    public function destroy(Talk $talk) {
        //
    }
}
