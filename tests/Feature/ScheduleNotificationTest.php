<?php

namespace Tests\Feature;

use App\Delegate;
use App\DelegateRole;
use App\Event;
use App\Jobs\ScheduleNotification;
use App\Notification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Tests\LogMailTestCase;
use Tests\MailCatcherTestCase;

class ScheduleNotificationTest extends MailCatcherTestCase
{
    use RefreshDatabase;

    private $event1;
    private $event2;

    protected function setUp() {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->event1 = factory(Event::class)->create();
        $this->event2 = factory(Event::class)->create();
    }

    protected function tearDown() {
//        $this->removeAllEmails();

        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    /**
     * @test
     */
    public function schedule_notification_job_pushed_to_queue() {
        Queue::fake();
        $delegatesCount = 3;
        $role = factory(DelegateRole::class)->create();
        $notification = factory(Notification::class)->create([
            'template' => 'test_transaction',
            'subject'  => "this is notification subject",
            'event_id' => $this->event1->id,
            'role_id'  => $role->id,
            'schedule' => Carbon::now()->addDays(-1),
            'is_sent'  => false
        ]);

        factory(Delegate::class, $delegatesCount)
            ->create(['event_id' => $this->event1->id])
            ->each(function (Delegate $delegate) use ($role) {
                $delegate->roles()->save($role);
            });

        Artisan::call('notification:schedule');

        // Perform order shipping...

        Queue::assertPushed(ScheduleNotification::class,
            function ($job) use ($notification) {
                return $job->notification->id === $notification->id;
            });

        Queue::assertPushedOn('email', ScheduleNotification::class);
    }


    /**
     * @test
     */
    public function send_schedule_notification_with_same_event_delegates_and_specified_role(
    ) {
        $role = factory(DelegateRole::class)->create();
        $role2 = factory(DelegateRole::class)->create();

        $notification = factory(Notification::class)->create([
            'template' => 'test_transaction',
            'subject'  => "this is notification subject",
            'event_id' => $this->event1->id,
            'role_id'  => $role->id,
            'schedule' => Carbon::now()->addDays(-1),
            'is_sent'  => false
        ]);

        $delegatesCount = 5;

        $delegates = factory(Delegate::class, $delegatesCount)
            ->create(['event_id' => $this->event1->id])
            ->each(function (Delegate $delegate) use ($role) {
                $delegate->roles()->save($role);
            });

        factory(Delegate::class, 2)
            ->create(['event_id' => $this->event1->id])
            ->each(function (Delegate $delegate) use ($role2) {
                $delegate->roles()->save($role2);
            });

        factory(Delegate::class, 2)
            ->create(['event_id' => $this->event2->id])
            ->each(function (Delegate $delegate) use ($role) {
                $delegate->roles()->save($role);
            });

        Artisan::call('notification:schedule');

        $this->assertDatabaseHas('notifications', [
            'id'      => $notification->id,
            'is_sent' => true,
        ]);

        $emails = $this->getAllEmail();


        $this->assertEquals($delegatesCount, count($emails));

//        $recipients = array_flatten(array_map(function ($mail) {
//            foreach ($mail as $line) {
//                if (strpos($line, "To:") === 0) {
//                    return trim(str_replace("To:", "", $line));
//                }
//            }
//        }, $emails));
//
//
//        $delegates->each(function (Delegate $delegate) use ($recipients
//        ) {
//            $this->assertTrue(in_array("{$delegate->email}",
//                $recipients));
//        });

    }

    /**
     * @test
     */
    public function not_sent_schedule_notification_will_trigger_again() {
        $role = factory(DelegateRole::class)->create();

        factory(Notification::class)->create([
            'template' => 'test_transaction',
            'subject'  => "this is notification subject",
            'event_id' => $this->event1->id,
            'role_id'  => $role->id,
            'schedule' => Carbon::now()->addDays(-1),
            'is_sent'  => false
        ]);

        $delegatesCount = 3;

        factory(Delegate::class, $delegatesCount)->create([
            'event_id' => $this->event1->id
        ])->each(function (Delegate $delegate) use ($role) {
            $delegate->roles()->save($role);
        });

        Artisan::call('notification:schedule');

        $this->removeAllEmails();

        Artisan::call('notification:schedule');

        $emails = $this->getAllEmail();

        $this->assertEquals(0, count($emails));

    }
}
