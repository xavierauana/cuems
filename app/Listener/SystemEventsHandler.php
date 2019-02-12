<?php

namespace App\Listener;

use App\Delegate;
use App\Enums\DelegateDuplicationStatus;
use App\Enums\SystemEvents;
use App\Event;
use App\Events\SystemEvent;
use App\Jobs\SendNotification;
use App\Notification;
use App\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

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
        Log::info("notifications " . $notifications->count());
        $notifications->filter(function (Notification $notification) use ($event
        ) {
            Log::info('duplicated check');
            if (!$notification->include_duplicated) {
                $model = ($event->model instanceof Delegate) ?
                    $event->model :
                    $event->model->payee;

                return $model->is_duplicated !== DelegateDuplicationStatus::DUPLICATED;
            }

            return true;
        })
                      ->filter(function (Notification $notification) use ($event
                      ) {
                          Log::info('verify check');
                          if ($notification->verified_only) {
                              $model = ($event->model instanceof Delegate) ?
                                  $event->model :
                                  $event->model->payee;

                              return $model->is_verified;
                          }

                          return true;
                      })
                      ->each(function (Notification $notification) use ($event
                      ) {
                          Log::info("going to dispatch the job");
                          if ($role = $notification->role) {
                              Log::info("with Role");
                              $this->dispatchJobWithNotificationRole($notification,
                                  $event,
                                  $role);
                          } else {
                              Log::info("without Role");
                              Log::info("and dispatch");
                              SendNotification::dispatch($notification,
                                  $event->model);
                              //                              $notification->send($event->model);
                          }
                      });

    }

    /**
     * @param \App\Events\SystemEvent $event
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    private function getNotification(SystemEvent $event): Collection {

        $e = $this->getEvent($event);

        if (is_null($e)) {
            throw new \Exception("No event fetched!");
        }
        $query = Notification::whereEventId($e->id);

        return $this->constructNotificationQuery($event, $query)->get();

    }

    /**
     * @param $role
     * @param $event
     * @return bool
     */
    private function delegateHasRole($role, $delegate): bool {

        return ($delegate instanceof Delegate) and in_array($role->id,
                $delegate->roles()
                         ->pluck('id')
                         ->toArray());
    }

    /**
     * @param $event
     * @param $role
     * @return bool
     */
    private function transactionPayeeHasRole($event, $role): bool {
        return $event->model instanceof Transaction and $this->delegateHasRole($role,
                $event->model->payee);
    }

    /**
     * @param \App\Notification $notification
     * @param                   $event
     * @param                   $role
     */
    private function dispatchJobWithNotificationRole(
        Notification $notification, $event, $role
    ): void {
        if ($this->delegateHasRole($role, $event->model)) {
            Log::info('delegate');
            SendNotification::dispatch($notification, $event->model);
            //            $notification->send($event->model);
        } elseif ($this->transactionPayeeHasRole($event, $role)) {
            Log::info('transaction');
            SendNotification::dispatch($notification, $event->model);
            //            $notification->send($event->model);
        }
    }

    /**
     * @param \App\Events\SystemEvent               $event
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Exception
     */
    private function constructNotificationQuery(
        SystemEvent $event, Builder $query
    ): Builder {
        switch ($event->systemEvent) {
            case SystemEvents::CREATE_DELEGATE:
                $query = $query->whereEvent(SystemEvents::CREATE_DELEGATE);
                break;
            case SystemEvents::ADMIN_CREATE_DELEGATE:
                $query = $query->whereEvent(SystemEvents::ADMIN_CREATE_DELEGATE);
                break;
            case SystemEvents::TRANSACTION_COMPLETED:
                $query = $query->whereEvent(SystemEvents::TRANSACTION_COMPLETED);
                break;
            case SystemEvents::TRANSACTION_FAILED:
                $query = $query->whereEvent(SystemEvents::TRANSACTION_FAILED);
                break;
            case SystemEvents::TRANSACTION_PENDING:
                $query = $query->whereEvent(SystemEvents::TRANSACTION_PENDING);
                break;
            case SystemEvents::TRANSACTION_REFUND:
                $query = $query->whereEvent(SystemEvents::TRANSACTION_REFUND);
                break;
            default:
                throw new \Exception("Invalided system event!");
        }

        return $query;
    }

    /**
     * @param \App\Events\SystemEvent $event
     * @return mixed
     * @throws \Exception
     */
    private function getEvent(SystemEvent $event): Event {

        switch (get_class($event->model)) {
            case Delegate::class:
                return $event->model->event;
            case Transaction::class:
                $payee = $event->model->payee;
                if ($payee instanceof Delegate) {
                    return $payee->event;
                }
                throw new \Exception("System event model is not valid");
            default:
                throw new \Exception("System event model is not valid");
        }

    }
}
