<?php
/**
 * Author: Xavier Au
 * Date: 7/12/2018
 * Time: 4:52 PM
 */

namespace App\Mail;


use App\Event;
use App\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

abstract class AbstractEventNotificationMail extends Mailable
{
    use Queueable;

    protected $notification;

    abstract public function __construct(
        Notification $notification, $notifiable, Event $event
    );

    abstract public function build();

    /**
     * @param $builder
     */
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

