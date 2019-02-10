<?php

namespace App\Mail;

use App\Enums\TransactionStatus;
use App\Event;
use App\Notification;
use App\Services\CreateTicketService;
use App\Transaction;
use Illuminate\Support\Facades\Log;

class NotificationMailable extends AbstractEventNotificationMail
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


        Log::info('going to check notification has ticket or not');

        $this->addAttachments();
    }
}
