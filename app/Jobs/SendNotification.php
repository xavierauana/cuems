<?php

namespace App\Jobs;

use App\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

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
        $queue = config('queue.default', null);
        if ($queue === 'redis') {
            $number = env('MAIL_THROTTLE_NUMBER');
            $duration = env('MAIL_THROTTLE_TIME');

            Redis::throttle('SendEmail')
                 ->allow($number)
                 ->every($duration)
                 ->then(function () {
                     $email = $this->notifiable->email;
                     Log::info('send ' . $email);
                     $this->notification->send($this->notifiable);

                 }, function () {
                     $email = $this->notifiable->email;
                     Log::info('postpone ' . $email);

                     return $this->release(10);
                 });
        } else {
            $this->notification->send($this->notifiable);
        }
    }
}
