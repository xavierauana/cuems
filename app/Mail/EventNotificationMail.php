<?php
/**
 * Author: Xavier Au
 * Date: 2019-03-06
 * Time: 18:16
 */

namespace App\Mail;


use App\CarbonCopy;
use App\Delegate;
use App\Enums\CarbonCopyType;
use App\Enums\TransactionStatus;
use App\Notification;
use App\Services\CreateTicketService;
use App\Transaction;

abstract class EventNotificationMail extends AbstractEventNotificationMail
{
    final public function addCarbonCopies(Notification $notification) {
        $notification->copies->each(function (CarbonCopy $copy) {
            if (CarbonCopyType::CC()->equals(new CarbonCopyType($copy->type))) {
                $this->cc($copy->email, $copy->name);
            } elseif (CarbonCopyType::BCC()
                                    ->equals(new CarbonCopyType($copy->type))) {
                $this->bcc($copy->email, $copy->name);
            }
        });
    }

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