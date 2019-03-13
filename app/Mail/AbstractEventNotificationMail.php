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

}

