<?php

namespace App\Mail;

use App\Event;
use App\Notification;
use Illuminate\Bus\Queueable;

class TransactionMail extends EventNotificationMail
{
    use Queueable;
    /**
     * @var string
     */
    protected $template;
    /**
     * @var \App\Transaction
     */
    protected $notifiable;
    /**
     * @var \App\Event
     */
    protected $event;
    /**
     * @var \App\Notification
     */
    protected $notification;

    /**
     * Create a new message instance.
     *
     * @param \App\Notification $notification
     * @param \App\Transaction  $transaction
     * @param \App\Event        $event
     */
    public function __construct(
        Notification $notification, $transaction, Event $event
    ) {
        //
        $this->notifiable = $transaction;
        $this->event = $event;
        $this->notification = $notification;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $builder = $this->view("notifications." . $this->notification->template,
            [
                'event'       => $this->event,
                'transaction' => $this->notifiable,
            ])
                        ->from($this->notification->from_email,
                            $this->notification->from_name)
                        ->subject($this->notification->subject);

        $this->addCarbonCopies($this->notification);

        $this->addAttachments();
    }
}
