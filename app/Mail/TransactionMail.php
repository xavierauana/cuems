<?php

namespace App\Mail;

use App\Event;
use App\Notification;
use App\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class TransactionMail extends Mailable
{
    use Queueable;
    /**
     * @var string
     */
    protected $template;
    /**
     * @var \App\Transaction
     */
    protected $transaction;
    /**
     * @var \App\Event
     */
    protected $event;
    /**
     * @var \App\Notification
     */
    private $notification;

    /**
     * Create a new message instance.
     *
     * @param \App\Notification $notification
     * @param \App\Transaction  $transaction
     * @param \App\Event        $event
     */
    public function __construct(
        Notification $notification, Transaction $transaction, Event $event
    ) {
        //
        $this->transaction = $transaction;
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
            'event'       => $this->event,
            'transaction' => $this->transaction,
        ])->from($this->notification->from_email,
            $this->notification->from_name)
                    ->subject($this->notification->subject);
    }
}
