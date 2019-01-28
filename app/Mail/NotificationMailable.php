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

        if ($cc = $this->notification->cc) {
            $this->cc($cc);
        }


        if ($bcc = $this->notification->bcc) {
            $this->bcc($bcc);
        }


        Log::info('going to check notification has ticket or not');

        if ($this->notification->include_ticket) {
            Log::info('notification has ticket');
            /** @var CreateTicketService $service */
            $service = app()->make(CreateTicketService::class)
                            ->setPageSize('a4')
                            ->setOrientation('portrait');

            $attachData = function (Transaction $transaction, $index) use (
                $service
            ) {
                $data = $service->createPDF($transaction);
                Log::info('create attached data');
                if (!is_null($data)) {

                    Log::info('attached data to notification');

                    $this->attachData($data, "ticket.pdf", [
                        'as'   => $this->delegate->getRegistrationId() . ".pdf",
                        'mime' => "application/pdf"
                    ]);
                }
            };

            $this->delegate->transactions()
                           ->whereStatus(TransactionStatus::COMPLETED)
                           ->get()
                           ->tap(
                               function ($collection) {
                                   $count = $collection->count();
                                   Log::info("there are {$count} notifications");
                               })->each($attachData);
        }

        $this->addAttachments();

        return $this;
    }
}
