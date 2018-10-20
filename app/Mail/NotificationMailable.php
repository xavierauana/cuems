<?php

namespace App\Mail;

use App\Delegate;
use App\Event;
use App\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class NotificationMailable extends Mailable
{
    use Queueable;

    /**
     * @var \App\Delegate
     */
    private $delegate;
    /**
     * @var \App\Event
     */
    private $event;
    /**
     * @var \App\Notification
     */
    private $notification;

    /**
     * Create a new message instance.
     *
     * @param \App\Notification $notification
     * @param \App\Delegate     $delegate
     * @param \App\Event        $event
     */
    public function __construct(
        Notification $notification, Delegate $delegate, Event $event
    ) {
        //
        $this->delegate = $delegate;
        $this->event = $event;
        $this->notification = $notification;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->view("notifications." . $this->notification->template, [
            'delegate' => $this->delegate,
            'event'    => $this->event
        ])->from($this->notification->from_email,
            $this->notification->from_name)
                    ->subject($this->notification->subject);
    }
}
