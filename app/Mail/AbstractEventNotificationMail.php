<?php
/**
 * Author: Xavier Au
 * Date: 7/12/2018
 * Time: 4:52 PM
 */

namespace App\Mail;


use App\Delegate;
use App\Enums\TransactionStatus;
use App\Event;
use App\Notification;
use App\Services\CreateTicketService;
use App\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

abstract class AbstractEventNotificationMail extends Mailable
{
    use Queueable;

    protected $notification;

    protected $notifiable;

    abstract public function __construct(
        Notification $notification, $notifiable, Event $event
    );

    abstract public function build();

    /**
     * @param $builder
     */
    protected function addAttachments(): void {
        $this->addTicket();
        $this->notification->uploadFiles->each(function ($storedFile) {
            if ($storedFile->disk === 'local') {
                $this->attach(storage_path("app/" . $storedFile->path));
            } else {
                throw new \Exception("No implementation other than local drive");
            }
        });
    }

    protected function addCc() {
        if ($cc = $this->notification->cc) {
            $this->cc($cc);
        }
    }

    protected function addBcc() {
        if ($bcc = $this->notification->bcc) {
            $this->bcc($bcc);
        }
    }

    private function addTicket() {

        if ($this->notification->include_ticket) {

            /** @var CreateTicketService $service */
            $service = app(CreateTicketService::class)->setPageSize('a4')
                                                      ->setOrientation('portrait');

            $attachData = function (Transaction $transaction) use ($service) {
                $data = $service->createPDF($transaction);
                if (!is_null($data)) {
                    $this->attachData($data, "ticket.pdf", [
                        'as'   => $transaction->payee->getRegistrationId() . ".pdf",
                        'mime' => "application/pdf"
                    ]);
                }
            };

            if ($this->notifiable instanceof Delegate) {
                $this->notifiable->transactions()
                                 ->whereStatus(TransactionStatus::COMPLETED)
                                 ->get()
                                 ->each($attachData);
            } elseif ($this->notifiable instanceof Transaction) {
                $attachData($this->notifiable);
            }
        }
    }
}

