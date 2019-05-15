<?php

namespace App\Jobs;

use App\Delegate;
use App\Notification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ScheduleNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * @var \App\Notification
     */
    public $delegate;
    /**
     * @var \App\Notification
     */
    public $notification;

    private $redisKey = "throttle_email";

    /**
     * Create a new job instance.
     *
     * @param \App\Notification $notification
     * @param \App\Delegate     $delegate
     */
    public function __construct(Notification $notification, Delegate $delegate
    ) {
        //
        $this->delegate = $delegate;
        $this->notification = $notification;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle() {

        if ($this->runRedisQueueJob()) {

            $allow = env('MAIL_THROTTLE_NUMBER', 2);
            $duration = env('MAIL_THROTTLE_TIME', 10);
            Redis::throttle($this->redisKey)
                 ->allow($allow)
                 ->every($duration)
                 ->then(function () {
                     Log::info(Carbon::now()->toDateTimeString());
                     $this->notification->sendNotificationToDelegate($this->delegate);
                 }, function () {
                     return $this->release(10);
                 });

        } else {
            Log::info(Carbon::now()->toDateTimeString());
            $this->notification->sendNotificationToDelegate($this->delegate);
        }

    }

    private function runRedisQueueJob(): bool {
        return env('QUEUE_CONNECTION') === 'redis';

    }
}
