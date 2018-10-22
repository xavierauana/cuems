<?php

namespace App\Http\Controllers;

use App\Delegate;
use App\Event;
use App\Http\Resources\SessionResource;
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
        $delegates = Delegate::excludeRole('default')
                             ->get();

        return view('admin.events.sessions.create',
            compact('event', 'delegates'));
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

        /** @var \App\Session $newSession */
        $newSession = $event->sessions()->create($validatedData);

        $newSession->setModerators($validatedData['moderators']);

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
     * @param Event         $event
     * @param  Session      $session
     * @param \App\Delegate $delegate
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Event $event, Session $session, Delegate $delegate) {

        $delegates = $delegate->excludeRole('default')->get()->reduce(function (
            $carry, $item
        ) {
            $carry[$item->name] = $item->id;

            return $carry;
        }, []);
        $delegates = $delegate->excludeRole('default')->get();

        return view("admin.events.sessions.edit",
            compact('event', 'session', 'delegates'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  Session $session
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event, Session $session) {
        $validatedData = $this->validate($request, Session::StoreRules,
            Session::ErrorMessages);

        $session->update($validatedData);
        $session->updateModerators($validatedData['moderators']);

        return redirect()->route("events.sessions.index", [$event, $session])
                         ->withStatus("Session: {$session->title} is updated!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Event $event
     * @param  Session   $session
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Event $event, Session $session) {

        $session->delete();

        return redirect()->route('events.sessions.index', $event)
                         ->withStatus("Session:{$session->title} is deleted!");
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Event               $event
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiGetSessions(Request $request, Event $event) {
        $sessions = $event->sessions()->with('talks')->get();
        $data = SessionResource::collection($sessions);

        return $data;
    }
}
