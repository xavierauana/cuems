<?php

namespace App\Http\Controllers;

use App\Event;
use App\Http\Resources\SessionResource;
use App\Session;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SessionsController extends Controller
{
    /**
     * @var Session
     */
    private $repo;

    /**
     * SessionsController constructor.
     * @param \App\Session $repo
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
        $delegates = $this->getDelegatesForModerators($event);

        return view('admin.events.sessions.create',
            compact('event', 'delegates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Event   $event
     * @return
     * @throws \Exception
     */
    public function store(Request $request, Event $event) {

        $validatedData = $this->validate($request, Session::StoreRules,
            Session::ErrorMessages);

        $validatedData['order'] = $validatedData['order'] ?? (($max = Session::max('order')) ? ($max + 1) : 1);

        DB::beginTransaction();

        try {

            /** @var \App\Session $newSession */
            $newSession = $event->sessions()->create($validatedData);

            if (isset($validatedData['moderators'])) {
                $newSession->setModerators($validatedData['moderators']);
            }

            $newSession->extra_attributes = $validatedData['extra_attributes'];

            $newSession->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route("events.sessions.index", $event)
                         ->withStatu('New session created!');
    }

    /**
     * Display the specified resource.
     *
     * @param Event   $event
     * @param Session $session
     * @return void
     */
    public function show(Event $event, Session $session) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Event   $event
     * @param Session $session
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Event $event, Session $session) {

        $delegates = $this->getDelegatesForModerators($event);

        return view("admin.events.sessions.edit",
            compact('event', 'session', 'delegates'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Session $session
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event, Session $session) {
        $validatedData = $this->validate($request, Session::StoreRules,
            Session::ErrorMessages);

        $validatedData['order'] = $validatedData['order'] ?? (($max = Session::max('order')) ? ($max + 1) : 1);

        $session->extra_attributes = $validatedData['extra_attributes'];
        $session->update($validatedData);
        $session->updateModerators($validatedData['moderators'] ?? null);

        return redirect()->route("events.sessions.index", [$event, $session])
                         ->withStatus("Session: {$session->title} is updated!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Event $event
     * @param Session    $session
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
     * @return JsonResource
     */
    public function apiGetSessions(Request $request, Event $event) {
        $sessions = $event->sessions()
                          ->with([
                              'talks' => function ($query) {
                                  return $query->orderBy('order');
                              },
                              'moderators',
                          ])
                          ->orderBy('order')
                          ->get();
        $data = SessionResource::collection($sessions);

        return $data;
    }

    public function apiSearchSessions(Request $request) {
        $keyword = $request->get('keyword');
        $sessions = Session::where('title', 'like', '%' . $keyword . '%')
                           ->orWhere('subtitle', 'like', '%' . $keyword . '%')
                           ->orWhere('sponsor', 'like', '%' . $keyword . '%')
                           ->orWhere('venue', 'like', '%' . $keyword . '%')
                           ->OrWithExtraAttributes(['description' => $keyword])
                           ->get();

        $data = SessionResource::collection($sessions);

        return $data;
    }

    /**
     * @param \App\Event $event
     * @return \Illuminate\Support\Collection
     */
    private function getDelegatesForModerators(Event $event): Collection {
        return $event->delegates()
                     ->excludeRole('default')
                     ->get();
        //        return $event->delegates()
        //                     ->excludeRole('default')
        //                     ->notDuplicated()
        //                     ->get();

    }
}
