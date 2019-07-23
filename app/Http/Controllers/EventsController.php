<?php

namespace App\Http\Controllers;

use App\Event;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $events = $this->repo->latest()->get();

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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function store(Request $request): RedirectResponse {

        $this->validate($request, Event::StoreRules, Event::ValidationMessages);

        DB::beginTransaction();

        try {

            $newEvent = $this->repo->create($request->all());

            $keys = config('event.settings', []);

            foreach ($keys as $key) {
                $newEvent->settings()->create(compact('key'));
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }


        return redirect()->route("events.index");
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Event $event
     * @return void
     */
    public function show(Event $event) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Event $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event) {
        return view("admin.events.edit", compact('event'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Event               $event
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Event $event) {
        $validatedData = $this->validate($request, Event::StoreRules,
            Event::ValidationMessages);

        $event->update($validatedData);

        return redirect()->route("events.index")
                         ->with("status", "Event updated!");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Event $event
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
