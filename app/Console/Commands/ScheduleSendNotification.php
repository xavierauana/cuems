<?php

namespace App\Console\Commands;

use App\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ScheduleSendNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled notification';
    /**
     * @var \App\Notification
     */
    private $notification;

    /**
     * Create a new command instance.
     *
     * @param \App\Notification $notification
     */
    public function __construct(Notification $notification) {
        parent::__construct();
        $this->notification = $notification;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $now = Carbon::now();

        Log::info('now:' . $now->toDateTimeString());
        $this->notification
            ->whereNotNull('schedule')
            ->whereIsSent(false)
            ->where('schedule', '<', $now)
            ->get()
            ->tap(function (Collection $collection) {
                Log::info("there are {$collection->count()} notifications");
                Log::info('going to send schedule notification');
            })
            ->each(function (Notification $notification) {
                Log::info('notification id: ' . $notification->id);
                $notification->setIsScheduleAction(true)
                             ->send();
            })->each->markSent();
    }
}
