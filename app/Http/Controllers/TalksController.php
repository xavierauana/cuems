<?php

namespace App\Http\Controllers;

use App\Delegate;
use App\Event;
use App\Http\Resources\TalkResource;
use App\Session;
use App\Talk;
use Illuminate\Http\Request;

class TalksController extends Controller
{

    private $repo;


    /**
     * TalksController constructor.
     * @param Talk $talk
     */
    public function __construct(Talk $talk) {
        $this->repo = $talk;
    }


    /**
     * Display a listing of the resource.
     *
     * @param Event   $event
     * @param Session $session
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
     * @param Event   $event
     * @param Session $session
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Event $event, Session $session) {

        $delegates = $this->getDelegates();

        return view('admin.events.sessions.talks.create',
            compact('event', 'session', 'delegates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Event                    $event
     * @param Session                  $session
     * @return
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, Event $event, Session $session) {

        $validatedData = $this->validate($request, Talk::StoreRules,
            Talk::ErrorMessages);

        $validatedData['order'] = $validatedData['order'] ?? (($max = Talk::max('order')) ? ($max + 1) : 1);

        /** @var Talk $newTalk */
        $newTalk = $session->talks()->create($validatedData);

        $newTalk->setSpeakers($validatedData['speakers']);

        $newTalk->extra_attributes = $validatedData['extra_attributes'];

        $newTalk->save();


        return redirect()->route('events.sessions.talks.index',
            [$event, $session])->withStatus('New talk created!');
    }

    /**
     * Display the specified resource.
     *
     * @param Talk $talk
     * @return \Illuminate\Http\Response
     */
    public function show(Talk $talk) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Event   $event
     * @param Session $session
     * @param Talk    $talk
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event, Session $session, Talk $talk) {
        $delegates = $this->getDelegates();

        return view("admin.events.sessions.talks.edit",
            compact('event', 'session', 'talk', 'delegates'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Event                    $event
     * @param Session                  $session
     * @param Talk                     $talk
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(
        Request $request, Event $event, Session $session, Talk $talk
    ) {

        $validatedData = $this->validate($request, Talk::StoreRules,
            Talk::ErrorMessages);

        $validatedData['order'] = $validatedData['order'] ?? (($max = Talk::max('order')) ? ($max + 1) : 1);

        $talk->extra_attributes = $validatedData['extra_attributes'];

        $talk->update($validatedData);

        $talk->updateSpeakers($validatedData['speakers']);

        return redirect()->route('events.sessions.talks.index',
            [$event, $session])->withStatus('New talk created!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Event   $event
     * @param Session $session
     * @param Talk    $talk
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Event $event, Session $session, Talk $talk) {

        $talk->delete();

        return redirect()->route("events.sessions.talks.index",
            [$event, $session])->withStatus("Talk:{$talk->title} is deleted!");

    }

    /**
     * @return mixed
     */
    private function getDelegates(): array {
        return Delegate::excludeRole('default')
                       ->get(['id as delegate_id', 'first_name', 'last_name'])
                       ->reduce(function ($carry, $delegate) {
                           $carry[$delegate->delegate_id] = $delegate->name;

                           return $carry;
                       }, []);
    }

    public function all(Event $event) {
        $talks = $event->sessions->map(function (Session $session) {
            return $session->talks()->with('session')->get();
        })->flatten();

        return view("admin.events.talks.index", compact('event', 'talks'));
    }

    /**
     * @param \App\Event               $event
     * @param \Illuminate\Http\Request $request
     */
    public function apiSearchTalks(Event $event, Request $request) {

        $keyword = $request->get('keyword');


        $collection = Talk::leftJoin('speakers', 'talks.id', '=',
            'speakers.talk_id')
                          ->leftJoin('delegates', 'speakers.delegate_id', '=',
                              'delegates.id')
                          ->select('talks.*', 'delegates.first_name',
                              'delegates.last_name')
                          ->whereIn('talks.session_id',
                              function ($query) use ($event) {
                                  $query->select('sessions.id')
                                        ->from('sessions')
                                        ->where('event_id', $event->id);
                              })
                          ->where('talks.extra_attributes->description', 'like',
                              "%{$keyword}%")
                          ->orWhere('talks.title', 'like',
                              "%{$keyword}%")
                          ->orWhere('delegates.first_name', 'like',
                              "%{$keyword}%")
                          ->orWhere('delegates.last_name', 'like',
                              "%{$keyword}%")
                          ->with('session')
                          ->get()->unique('id');


        return TalkResource::collection($collection);

    }

}
