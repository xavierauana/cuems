<?php

namespace App\Http\Controllers;

use App\Event;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingsControllers extends Controller
{
    /**
     * @var \App\Setting
     */
    private $repo;

    /**
     * SettingsControllers constructor.
     * @param \App\Setting $setting
     */
    public function __construct(Setting $setting) {
        $this->repo = $setting;
    }


    /**
     * Display a listing of the resource.
     *
     * @param  \App\Event $event
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Event $event) {
        $settings = $event->settings()
                          ->search($request->query('keywords'))
                          ->paginate(100);

        return view("admin.events.settings.index",
            compact('event', 'settings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Event $event
     * @return \Illuminate\Http\Response
     */
    public function create(Event $event) {
        return view("admin.events.settings.create", compact('event'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Event               $event
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, Event $event) {
        $validatedData = $this->validate($request, [
            'key'   => [
                "required",
                Rule::unique('settings')->where(function ($query) use ($event) {
                    return $query->where('event_id', $event->id);
                })
            ],
            'value' => "required",
        ]);
        $event->settings()->create($validatedData);

        return redirect()->route('events.settings.index', $event)
                         ->withStatus("{$validatedData['key']} is created!");

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Event   $event
     * @param  \App\Setting $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event, Setting $setting) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Event   $event
     * @param  \App\Setting $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event, Setting $setting) {
        return view("admin.events.settings.edit", compact("event", 'setting'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Event               $event
     * @param  \App\Setting             $setting
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Event $event, Setting $setting) {
        $validatedData = $this->validate($request, [
            'key'   => [
                "required",
                Rule::unique('settings')
                    ->ignore($setting->id, 'id')
                    ->where(function ($query) use ($event) {
                        return $query->where('event_id', $event->id);
                    })
            ],
            'value' => "required",
        ]);
        $setting->update($validatedData);

        return redirect()->route('events.settings.index', $event)
                         ->withStatus("{$validatedData['key']} is updated!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Event   $event
     * @param  \App\Setting $setting
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Event $event, Setting $setting) {
        $event->settings()->find($setting->id)->delete();

        return response()->json(['status' => 'completed']);
    }

    public function search(Request $request, Event $event) {
        $settings = $event->settings()
                          ->search($request->query('keywords'))
                          ->paginate(100);

        return $request->ajax() ?
            response()->json($settings) :
            view("admin.events.settings.index",
                compact('event', 'settings'));
    }
}
