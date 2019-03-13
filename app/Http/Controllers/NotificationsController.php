<?php

namespace App\Http\Controllers;

use App\Enums\SystemEvents;
use App\Event;
use App\Http\Requests\NotificationStoreRequest;
use App\Http\Requests\NotificationUpdateRequest;
use App\Notification;
use App\Services\TestNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $files = $event->uploadFiles()->pluck('name', 'id');

        return view("admin.events.notifications.create",
            compact('event', 'events', 'files'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \App\Event                $event
     * @return void
     */
    public function store(NotificationStoreRequest $request, Event $event) {

        $validatedData = $request->validated();

        DB::beginTransaction();

        try {

            $newNotification = $event->notifications()->create($validatedData);

            foreach (Notification::parseEmailString($validatedData['cc']) as $email) {
                $newNotification->addCc($email);
            }

            foreach (Notification::parseEmailString($validatedData['bcc']) as $email) {
                $newNotification->addBcc($email);
            }

            if (isset($validatedData['files'])) {
                $newNotification->uploadFiles()->sync($validatedData['files']);
            } else {
                $newNotification->uploadFiles()->sync([]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


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
        $files = $event->uploadFiles()->pluck('name', 'id');

        return view("admin.events.notifications.edit",
            compact("event", "events", "notification", "files"));
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
        NotificationUpdateRequest $request, Event $event,
        Notification $notification
    ) {

        $validatedData = $request->validated();

        DB::beginTransaction();

        try {

            $notification->update($validatedData);

            $notification->syncCc(Notification::parseEmailString($validatedData['cc']));

            $notification->syncBcc(Notification::parseEmailString($validatedData['bcc']));

            if (isset($validatedData['files'])) {
                $notification->uploadFiles()->sync($validatedData['files']);
            } else {
                $notification->uploadFiles()->sync([]);
            }


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


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
     * @throws \Illuminate\Validation\ValidationException
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
     * @param \App\Event                            $event
     * @param \App\Notification                     $notification
     * @param \App\Services\TestNotificationService $service
     * @param \Illuminate\Http\Request              $request
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function test(
        Event $event,
        TestNotificationService $service,
        Request $request
    ) {

        $this->validate($request, [
            'email'           => 'required|email',
            'notification_id' => 'required|exists:notifications,id',
        ]);

        /** @var \App\Notification $notification */
        $notification = $event->notifications()
                              ->findOrFail($request->get('notification_id'));

        $service = $service->setNotification($notification)
                           ->setTestEmail($request->get('email'));

        switch ($notification->event) {
            case SystemEvents::ADMIN_CREATE_DELEGATE;
            case SystemEvents::CREATE_DELEGATE:
                $service->testDelegate();
                break;
            case SystemEvents::TRANSACTION_COMPLETED;
            case SystemEvents::TRANSACTION_FAILED;
            case SystemEvents::TRANSACTION_PENDING;
            case SystemEvents::TRANSACTION_REFUND:
                $service->testTransaction();
                break;
        }

        return redirect()->back()->withStatus('Test notification sent!');

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

        $keys = array_prepend($keys, "-- Please Select --");
        $values = array_prepend($values, 0);

        $events = array_combine($values, $keys);

        return $events;
    }


}
