<?php

namespace App\Http\Controllers;

use App\Enums\SystemEvents;
use App\Event;
use App\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationsController extends Controller
{
    /**
     * @var \App\Notification
     */
    private $repo;

    /**
     * NotificationsController constructor.
     * @param \App\Notification $repo
     */
    public function __construct(Notification $repo) {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Event $event
     * @return void
     */
    public function index(Event $event) {
        $event->load("notifications");

        return view("admin.events.notifications.index",
            compact('event'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \App\Event $event
     * @return void
     */
    public function create(Event $event) {

        $events = $this->getFormattedEvents();

        return view("admin.events.notifications.create",
            compact('event', 'events'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \App\Event                $event
     * @return void
     */
    public function store(Request $request, Event $event) {
        $validatedData = $this->validate($request,
            $this->repo->getStoreRules());

        $newNotification = $event->notifications()->create($validatedData);

        return redirect()->route("events.notifications.index", $event)
                         ->withStatus("Notification: {$newNotification->name} is created!");
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Event         $event
     * @param  \App\Notification $notification
     * @return void
     */
    public function show(Event $event, Notification $notification) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Event         $event
     * @param  \App\Notification $notification
     * @return void
     */
    public function edit(Event $event, Notification $notification) {
        $events = $this->getFormattedEvents();

        return view("admin.events.notifications.edit",
            compact("event", "events", "notification"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \App\Event                $event
     * @param  \App\Notification        $notification
     * @return void
     */
    public function update(
        Request $request, Event $event, Notification $notification
    ) {
        $validatedData = $this->validate($request,
            $this->repo->getStoreRules());

        $notification->update($validatedData);

        return redirect()->route("events.notifications.index", $event)
                         ->withStatus("Notification: {$notification->name} is updated!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Event         $event
     * @param  \App\Notification $notification
     * @return void
     */
    public function destroy(Event $event, Notification $notification) {
        //
    }

    /**
     * Upload email template
     *
     * @param \App\Event $event
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getImport(Event $event) {

        return view("admin.events.notifications.import", compact('event'));
    }

    /**
     * Upload email template
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Event               $event
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postImport(Request $request, Event $event
    ): RedirectResponse {

        Validator::extend('isBladeFile',
            function ($attribute, $value, $parameters, $validator) {
                $ext = ".blade.php";
                $fileName = $value->getClientOriginalName();
                $fileEnds = substr($fileName, -(strlen($ext)));

                return $fileEnds === $ext;
            }, "Template file must be a .blade.php file!");

        $this->validate($request, [
            'file' => 'required|min:0|isBladeFile'
        ]);


        $file = $request->file('file');
        $name = $file->getClientOriginalName();

        $file->move(resource_path("views/notifications"),
            $name);

        $message = "Template {$name} is uploaded!";

        return redirect()->route("events.notifications.index", $event)
                         ->withStatus($message);
    }

    /**
     * @return array
     */
    private function getFormattedEvents(): array {
        $events = SystemEvents::getEvents();

        $keys = array_keys($events);
        $values = array_values($events);
        $keys = array_map(function (string $value) {
            $newString = str_replace("_", " ", $value);

            return ucwords(strtolower($newString));
        }, $keys);
        $events = array_combine($values, $keys);

        return $events;
    }

}
