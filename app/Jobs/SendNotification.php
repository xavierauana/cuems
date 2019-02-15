<?php

namespace App\Jobs;

use App\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;
    /**
     * @var \App\Notification
     */
    protected $notification;
    /**
     * @var null
     */
    protected $notifiable;


    /**
     * Create a new job instance.
     *
     * @param \App\Notification $notification
     * @param null              $notifiable
     */
    public function __construct(
        Notification $notification, $notifiable = null
    ) {
        //
        $this->notification = $notification;
        $this->notifiable = $notifiable;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $this->notification->send($this->notifiable);
    }
}
