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
    private $delegate;
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
        $this->delegate = $delegate;
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
                'delegate' => $this->delegate,
                'event'    => $this->event
            ])->from($this->notification->from_email,
            $this->notification->from_name)
             ->subject($this->notification->subject);

        Log::info('going to check notification has ticket or not');

        if ($this->notification->include_ticket) {
            Log::info('notification has ticket');
            /** @var CreateTicketService $service */
            $service = app()->make(CreateTicketService::class)
                            ->setPageSize('a4')
                            ->setOrientation('portrait');

            $transactions = $this->delegate->transactions()
                                           ->whereStatus(TransactionStatus::COMPLETED)
                                           ->get();

            $transactions->each(function (Transaction $transaction, $index) use
            (
                $service
            ) {
                if ($data = $service->createPDF($transaction)) {

                    Log::info('attached data to notification');

                    $this->attachData($data, "ticket.pdf", [
                        'mime' => "application/pdf"
                    ]);
                }
            });
        }

        $this->addAttachments();

        return $this;
    }
}
