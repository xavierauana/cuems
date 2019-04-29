<?php

namespace App\Mail;

use App\Event;
use App\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificationMailable extends EventNotificationMail
{

    /**
     * @var \App\Delegate
     */
    protected $notifiable;
    /**
     * @var \App\Event
     */
    private $event;
    /**
     * @var \App\Notification
     */
    protected $notification;

    /**
     * Create a new message instance.
     *
     * @param \App\Notification                   $notification
     * @param \App\Delegate                       $delegate
     * @param \App\Event                          $event
     * @param \Illuminate\Support\Collection|null $attachments
     */
    public function __construct(
        Notification $notification, $delegate, Event $event
    ) {
        //
        $this->notifiable = $delegate;
        $this->event = $event;
        $this->notification = $notification;
        $this->includeTicket = $notification->include_ticket;
    }

    /**
     * Build the message.
     *
     * @param null   $data
     * @param string $attachmentName
     * @param string $mimeType
     * @return void
     */
    public function build() {
        $this->view("notifications." . $this->notification->template,
            [
                'delegate' => $this->notifiable,
                'event'    => $this->event
            ])->from($this->notification->from_email,
            $this->notification->from_name)
             ->subject($this->notification->subject);

        $this->addCarbonCopies($this->notification);

        $this->addAttachments();
    }
}
