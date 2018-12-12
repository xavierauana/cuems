<?php

namespace App\Mail;

use App\Enums\TransactionStatus;
use App\Event;
use App\Notification;
use App\Services\CreateTicketService;
use App\Transaction;
use Illuminate\Support\Facades\Mail;

class NotificationMailable extends Mail
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

        if ($this->notification->include_ticket) {

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

        $this->addAttachments();

        return $this;
    }

    protected function addAttachments(): void {
        $this->notification->uploadFiles->each(function ($storedFile) {
            if ($storedFile->disk === 'local') {
                $this->attach(storage_path("app/" . $storedFile->path));
            } else {
                throw new \Exception("No implementation other than local drive");
            }
        });

    }
}
