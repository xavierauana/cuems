<?php

namespace App\Mail;

use App\Delegate;
use App\Enums\TransactionStatus;
use App\Event;
use App\Notification;
use App\Services\CreateTicketService;
use App\Transaction;
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
     * @var bool
     */
    private $includeTicket;

    /**
     * Create a new message instance.
     *
     * @param \App\Notification $notification
     * @param \App\Delegate     $delegate
     * @param \App\Event        $event
     * @param bool              $includeTicket
     */
    public function __construct(
        Notification $notification, Delegate $delegate, Event $event,
        bool $includeTicket = false
    ) {
        //
        $this->delegate = $delegate;
        $this->event = $event;
        $this->notification = $notification;
        $this->includeTicket = $includeTicket;
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
        $builder = $this->view("notifications." . $this->notification->template,
            [
                'delegate' => $this->delegate,
                'event'    => $this->event
            ])->from($this->notification->from_email,
            $this->notification->from_name)
                        ->subject($this->notification->subject);

        if ($this->includeTicket) {

            /** @var CreateTicketService $service */
            $service = app()->make(CreateTicketService::class);

            $transactions = $this->delegate->transactions()
                                           ->whereStatus(TransactionStatus::COMPLETED)
                                           ->get();


            $transactions->each(function (Transaction $transaction, $index) use
            (
                &$builder, $service
            ) {
                $data = $service->createPDF($transaction);
                $builder->attachData($data, "ticket.pdf", [
                    'mime' => "application/pdf"
                ]);
            });
        }

        return $builder;
    }
}
