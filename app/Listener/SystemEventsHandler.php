<?php

namespace App\Listener;

use App\Delegate;
use App\Enums\SystemEvents;
use App\Events\SystemEvent;
use App\Jobs\SendNotification;
use App\Notification;
use App\Transaction;
use Illuminate\Support\Collection;

class SystemEventsHandler
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SystemEvent $event
     * @return void
     */
    public function handle(SystemEvent $event) {
        $notifications = $this->getNotification($event);

        $notifications->each(function (Notification $notification) use ($event
        ) {
            if ($role = $notification->role) {
                $this->dispatchJobWithNotificationRole($notification, $event,
                    $role);
            } else {
                dispatch(new SendNotification($notification, $event->model));
            }
        });

    }

    private function getNotification(SystemEvent $event): Collection {
        switch ($event->event) {
            case SystemEvents::CREATE_DELEGATE:
                return Notification::whereEvent(SystemEvents::CREATE_DELEGATE)
                                   ->get();
            case SystemEvents::ADMIN_CREATE_DELEGATE:
                return Notification::whereEvent(SystemEvents::ADMIN_CREATE_DELEGATE)
                                   ->get();
            case SystemEvents::TRANSACTION_COMPLETED:
                return Notification::whereEvent(SystemEvents::TRANSACTION_COMPLETED)
                                   ->get();
            case SystemEvents::TRANSACTION_FAILED:
                return Notification::whereEvent(SystemEvents::TRANSACTION_FAILED)
                                   ->get();
            case SystemEvents::TRANSACTION_PENDING:
                return Notification::whereEvent(SystemEvents::TRANSACTION_PENDING)
                                   ->get();
            case SystemEvents::TRANSACTION_REFUND:
                return Notification::whereEvent(SystemEvents::TRANSACTION_REFUND)
                                   ->get();
            default:
                return new Collection();
        }

    }

    /**
     * @param $role
     * @param $event
     * @return bool
     */
    private function delegateHasRole($role, Delegate $delegate): bool {
        return in_array($role->id, $delegate->roles()
                                            ->pluck('id')
                                            ->toArray());
    }

    /**
     * @param $event
     * @param $role
     * @return bool
     */
    private function transactionPayeeHasRole($event, $role): bool {
        return $event->model instanceof Transaction and
               $event->model->payee_type === Delegate::class and
               $this->delegateHasRole($role, $event->model->payee);
    }

    /**
     * @param \App\Notification $notification
     * @param                   $event
     * @param                   $role
     */
    private function dispatchJobWithNotificationRole(
        Notification $notification, $event, $role
    ): void {
        if ($event->model instanceof Delegate and
            $this->delegateHasRole($role, $event->model)) {
            dispatch(new SendNotification($notification,
                $event->model));
        } elseif ($this->transactionPayeeHasRole($event, $role)) {
            dispatch(new SendNotification($notification,
                $event->model));
        }
    }
}
