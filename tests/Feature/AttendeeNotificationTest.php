<?php

namespace Tests\Feature;

use App\Delegate;
use App\DelegateRole;
use App\Event;
use App\Jobs\ScheduleNotification;
use App\Notification;
use App\Ticket;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AttendeeNotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function create_notification() {

        $this->withoutExceptionHandling();

        Carbon::setTestNow(Carbon::createMidnightDate(2019, 1, 1));

        $user = factory(User::class)->create();

        $this->actingAs($user);

        $event = factory(Event::class)->create();

        $data = [
            'name'          => 'Attendee Notification Test',
            'from_name'     => 'Xavier',
            'from_email'    => 'xavier.au@gmail.com',
            'subject'       => 'this is a test',
            'check_in_date' => Carbon::now()->toDateString(),
            'keyword'       => 'test_keyword',
            'template'      => 'test_transaction',
            'type'          => 'attendee',
            'schedule'      => Carbon::now()->addDay()->format('d M Y H:i'),
            'event_id'      => $event->id,
        ];

        $uri = route('events.checkinRecords.notification', $event);

        $this->post($uri, $data);

        $this->assertDatabaseHas('notifications', [
            'name'          => $data['name'],
            'from_name'     => $data['from_name'],
            'from_email'    => $data['from_email'],
            'subject'       => $data['subject'],
            'check_in_date' => Carbon::now()->toDateTimeString(),
            'keyword'       => $data['keyword'],
            'type'          => $data['type'],
        ]);
    }

    /**
     * @test
     */
    public function attendee_notification_no_delegates() {

        Queue::fake();

        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->actingAs($user);

        $event = factory(Event::class)->create();

        $user = factory(User::class)->create();

        factory(Delegate::class, 10)
            ->create(['event_id' => $event->id])
            ->map(function (Delegate $delegate) use ($event) {
                return factory(Transaction::class)->create([
                    'ticket_id' => factory(Ticket::class)->create([
                        'event_id' => $event->id
                    ])->id,
                    'payee_id'  => $delegate->id
                ]);
            });

        $data = [
            'name'          => 'Attendee Notification Test',
            'from_name'     => 'Xavier',
            'from_email'    => 'xavier.au@gmail.com',
            'subject'       => 'this is a test',
            'check_in_date' => null,
            'keyword'       => null,
            'template'      => 'test_transaction',
            'type'          => 'attendee',
            'schedule'      => Carbon::now()->addDay()->format('d M Y H:i'),
        ];

        $uri = route('events.checkinRecords.notification', $event);

        $this->post($uri, $data);

        $this->assertDatabaseHas('notifications', [
            'name'          => $data['name'],
            'from_name'     => $data['from_name'],
            'from_email'    => $data['from_email'],
            'subject'       => $data['subject'],
            'check_in_date' => $data['check_in_date'],
            'keyword'       => $data['keyword'],
            'template'      => $data['template'],
            'type'          => $data['type'],
        ]);

        $notifications = Notification::whereType('attendee')->get();

        $this->assertEquals(1, $notifications->count());

        /** @var \App\Notification $notification */
        $notification = $notifications->first();

        $notification->setIsScheduleAction(true)
                     ->send();

        Queue::assertNothingPushed();
    }

    /**
     * @test
     */
    public function attendee_notification_all_checkined_delegates() {

        Queue::fake();

        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->actingAs($user);

        $event = factory(Event::class)->create();

        $user = factory(User::class)->create();

        factory(Delegate::class, 10)
            ->create(['event_id' => $event->id])
            ->map(function (Delegate $delegate) use ($event) {
                return factory(Transaction::class)->create([
                    'ticket_id' => factory(Ticket::class)->create([
                        'event_id' => $event->id
                    ])->id,
                    'payee_id'  => $delegate->id
                ]);
            })
            ->each(function (Transaction $transaction) use ($user) {
                DB::table('check_in')->insert([
                    'transaction_id' => $transaction->id,
                    'user_id'        => $user->id,
                    'created_at'     => Carbon::now()
                ]);
            });

        $data = [
            'name'          => 'Attendee Notification Test',
            'from_name'     => 'Xavier',
            'from_email'    => 'xavier.au@gmail.com',
            'subject'       => 'this is a test',
            'check_in_date' => null,
            'keyword'       => null,
            'template'      => 'test_transaction',
            'type'          => 'attendee',
            'schedule'      => Carbon::now()->addDay()->format('d M Y H:i'),
        ];

        $uri = route('events.checkinRecords.notification', $event);

        $this->post($uri, $data);

        $this->assertDatabaseHas('notifications', [
            'name'          => $data['name'],
            'from_name'     => $data['from_name'],
            'from_email'    => $data['from_email'],
            'subject'       => $data['subject'],
            'check_in_date' => $data['check_in_date'],
            'keyword'       => $data['keyword'],
            'template'      => $data['template'],
            'type'          => $data['type'],
        ]);

        $notifications = Notification::whereType('attendee')->get();

        $this->assertEquals(1, $notifications->count());

        /** @var \App\Notification $notification */
        $notification = $notifications->first();

        $notification->setIsScheduleAction(true)
                     ->send();

        Queue::assertPushedOn('email', ScheduleNotification::class);
        Queue::assertPushed(ScheduleNotification::class, 10);
    }

    /**
     * @test
     */
    public function attendee_notification_check_in_specific_date() {

        Queue::fake();

        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->actingAs($user);

        $event = factory(Event::class)->create();

        $user = factory(User::class)->create();

        $number = 3;

        factory(Delegate::class, 10)
            ->create(['event_id' => $event->id])
            ->map(function (Delegate $delegate) use ($event) {
                return factory(Transaction::class)->create([
                    'ticket_id' => factory(Ticket::class)->create([
                        'event_id' => $event->id
                    ])->id,
                    'payee_id'  => $delegate->id
                ]);
            })
            ->each(function (Transaction $transaction, $index) use (
                $user, $number
            ) {
                $date = $index < $number ? Carbon::now() : Carbon::now()
                                                                 ->addDay();

                DB::table('check_in')->insert([
                    'transaction_id' => $transaction->id,
                    'user_id'        => $user->id,
                    'created_at'     => $date
                ]);
            });

        $data = [
            'name'          => 'Attendee Notification Test',
            'from_name'     => 'Xavier',
            'from_email'    => 'xavier.au@gmail.com',
            'subject'       => 'this is a test',
            'check_in_date' => Carbon::now(),
            'keyword'       => null,
            'template'      => 'test_transaction',
            'type'          => 'attendee',
            'schedule'      => Carbon::now()->addDay()->format('d M Y H:i'),
        ];

        $uri = route('events.checkinRecords.notification', $event);

        $this->post($uri, $data);

        $this->assertDatabaseHas('notifications', [
            'name'          => $data['name'],
            'from_name'     => $data['from_name'],
            'from_email'    => $data['from_email'],
            'subject'       => $data['subject'],
            'check_in_date' => $data['check_in_date'],
            'keyword'       => $data['keyword'],
            'template'      => $data['template'],
            'type'          => $data['type'],
        ]);

        $notifications = Notification::whereType('attendee')->get();

        $this->assertEquals(1, $notifications->count());

        /** @var \App\Notification $notification */
        $notification = $notifications->first();

        $notification->setIsScheduleAction(true)
                     ->send();

        Queue::assertPushedOn('email', ScheduleNotification::class);
        Queue::assertPushed(ScheduleNotification::class, $number);
    }


    /**
     * @test
     */
    public function attendee_notification_check_in_specific_ticket_name() {

        Queue::fake();

        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->actingAs($user);

        $event = factory(Event::class)->create();

        $user = factory(User::class)->create();

        $number = 3;

        $tickets = factory(Ticket::class, 2)->create([
            'event_id' => $event->id
        ]);

        factory(Delegate::class, 10)
            ->create(['event_id' => $event->id])
            ->map(function (Delegate $delegate, $index) use (
                $event, $tickets, $number
            ) {
                $ticket = $index < $number ? $tickets[0] : $tickets[1];

                return factory(Transaction::class)->create([
                    'ticket_id' => $ticket->id,
                    'payee_id'  => $delegate->id
                ]);
            })
            ->each(function (Transaction $transaction) use ($user) {
                $date = Carbon::now();

                DB::table('check_in')->insert([
                    'transaction_id' => $transaction->id,
                    'user_id'        => $user->id,
                    'created_at'     => $date
                ]);
            });

        $data = [
            'name'          => 'Attendee Notification Test',
            'from_name'     => 'Xavier',
            'from_email'    => 'xavier.au@gmail.com',
            'subject'       => 'this is a test',
            'check_in_date' => null,
            'keyword'       => $tickets->first()->name,
            'template'      => 'test_transaction',
            'type'          => 'attendee',
            'schedule'      => Carbon::now()->addDay()->format('d M Y H:i'),
        ];

        $uri = route('events.checkinRecords.notification', $event);

        $this->post($uri, $data);

        $this->assertDatabaseHas('notifications', [
            'name'          => $data['name'],
            'from_name'     => $data['from_name'],
            'from_email'    => $data['from_email'],
            'subject'       => $data['subject'],
            'check_in_date' => $data['check_in_date'],
            'keyword'       => $data['keyword'],
            'template'      => $data['template'],
            'type'          => $data['type'],
        ]);

        $notifications = Notification::whereType('attendee')->get();

        $this->assertEquals(1, $notifications->count());

        /** @var \App\Notification $notification */
        $notification = $notifications->first();

        $notification->setIsScheduleAction(true)
                     ->send();

        Queue::assertPushedOn('email', ScheduleNotification::class);
        Queue::assertPushed(ScheduleNotification::class, $number);
    }


    /**
     * @test
     */
    public function attendee_notification_check_in_specific_ticket_name_and_date(
    ) {

        Queue::fake();

        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->actingAs($user);

        $event = factory(Event::class)->create();

        $user = factory(User::class)->create();

        $number = 3;

        $tickets = factory(Ticket::class, 2)->create([
            'event_id' => $event->id
        ]);

        factory(Delegate::class, 10)
            ->create(['event_id' => $event->id])
            ->map(function (Delegate $delegate, $index) use (
                $event, $tickets, $number
            ) {
                $ticket = $index < ($number + 2) ? $tickets[0] : $tickets[1];

                return factory(Transaction::class)->create([
                    'ticket_id' => $ticket->id,
                    'payee_id'  => $delegate->id
                ]);
            })
            ->each(function (Transaction $transaction, $index) use (
                $user, $number
            ) {
                $date = $index < ($number) ? Carbon::now() : Carbon::now()
                                                                   ->addDay();
                DB::table('check_in')->insert([
                    'transaction_id' => $transaction->id,
                    'user_id'        => $user->id,
                    'created_at'     => $date
                ]);
            });

        $data = [
            'name'          => 'Attendee Notification Test',
            'from_name'     => 'Xavier',
            'from_email'    => 'xavier.au@gmail.com',
            'subject'       => 'this is a test',
            'check_in_date' => Carbon::now(),
            'keyword'       => $tickets->first()->name,
            'template'      => 'test_transaction',
            'type'          => 'attendee',
            'schedule'      => Carbon::now()->addDay()->format('d M Y H:i'),
        ];

        $uri = route('events.checkinRecords.notification', $event);

        $this->post($uri, $data);

        $this->assertDatabaseHas('notifications', [
            'name'          => $data['name'],
            'from_name'     => $data['from_name'],
            'from_email'    => $data['from_email'],
            'subject'       => $data['subject'],
            'check_in_date' => $data['check_in_date'],
            'keyword'       => $data['keyword'],
            'template'      => $data['template'],
            'type'          => $data['type'],
        ]);

        $notifications = Notification::whereType('attendee')->get();

        $this->assertEquals(1, $notifications->count());

        /** @var \App\Notification $notification */
        $notification = $notifications->first();

        $notification->setIsScheduleAction(true)
                     ->send();

        Queue::assertPushedOn('email', ScheduleNotification::class);
        Queue::assertPushed(ScheduleNotification::class, $number);
    }

    /**
     * @test
     */
    public function attendee_notification_check_in_specific_ticket_name_and_date_and_role(
    ) {

        Queue::fake();

        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->actingAs($user);

        $event = factory(Event::class)->create();

        $user = factory(User::class)->create();

        $number = 3;

        $tickets = factory(Ticket::class, 2)->create([
            'event_id' => $event->id
        ]);

        $roles = factory(DelegateRole::class, 2)->create();

        factory(Delegate::class, 10)
            ->create(['event_id' => $event->id])
            ->each(function (Delegate $delegate, $index) use (
                $event, $roles, $number
            ) {
                $role = $index < ($number) ? $roles[0] : $roles[1];
                $delegate->roles()->save($role);
            })
            ->map(function (Delegate $delegate, $index) use (
                $event, $tickets, $number
            ) {
                $ticket = $index < ($number + 2) ? $tickets[0] : $tickets[1];

                return factory(Transaction::class)->create([
                    'ticket_id' => $ticket->id,
                    'payee_id'  => $delegate->id
                ]);
            })
            ->each(function (Transaction $transaction, $index) use (
                $user, $number
            ) {
                $date = $index < ($number + 2) ? Carbon::now() : Carbon::now()
                                                                       ->addDay();
                DB::table('check_in')->insert([
                    'transaction_id' => $transaction->id,
                    'user_id'        => $user->id,
                    'created_at'     => $date
                ]);
            });

        $data = [
            'name'          => 'Attendee Notification Test',
            'from_name'     => 'Xavier',
            'from_email'    => 'xavier.au@gmail.com',
            'subject'       => 'this is a test',
            'check_in_date' => Carbon::now(),
            'keyword'       => $tickets->first()->name,
            'template'      => 'test_transaction',
            'type'          => 'attendee',
            'schedule'      => Carbon::now()->addDay()->format('d M Y H:i'),
            'role_id'       => $roles[0]->id
        ];

        $uri = route('events.checkinRecords.notification', $event);

        $this->post($uri, $data);

        $this->assertDatabaseHas('notifications', [
            'name'          => $data['name'],
            'from_name'     => $data['from_name'],
            'from_email'    => $data['from_email'],
            'subject'       => $data['subject'],
            'check_in_date' => $data['check_in_date'],
            'keyword'       => $data['keyword'],
            'template'      => $data['template'],
            'type'          => $data['type'],
        ]);

        $notifications = Notification::whereType('attendee')->get();

        $this->assertEquals(1, $notifications->count());

        /** @var \App\Notification $notification */
        $notification = $notifications->first();

        $notification->setIsScheduleAction(true)
                     ->send();

        Queue::assertPushedOn('email', ScheduleNotification::class);
        Queue::assertPushed(ScheduleNotification::class, $number);
    }

    /**
     * @test
     */
    public function notification_send_with_schedule() {

        Queue::fake();

        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->actingAs($user);

        $event = factory(Event::class)->create();

        $user = factory(User::class)->create();

        $number = 3;

        $ticket = factory(Ticket::class)->create([
            'event_id' => $event->id
        ]);

        factory(Delegate::class, $number)
            ->create(['event_id' => $event->id])
            ->map(function (Delegate $delegate) use (
                $event, $ticket
            ) {
                return factory(Transaction::class)->create([
                    'ticket_id' => $ticket->id,
                    'payee_id'  => $delegate->id
                ]);
            })
            ->each(function (Transaction $transaction, $index) use (
                $user, $number
            ) {
                $date = Carbon::now();
                DB::table('check_in')->insert([
                    'transaction_id' => $transaction->id,
                    'user_id'        => $user->id,
                    'created_at'     => $date
                ]);
            });

        Carbon::setTestNow(Carbon::create(2019, 01, 01, 0, 0));

        $data = [
            'name'          => 'Attendee Notification Test',
            'from_name'     => 'Xavier',
            'from_email'    => 'xavier.au@gmail.com',
            'subject'       => 'this is a test',
            'check_in_date' => null,
            'keyword'       => $ticket->name,
            'template'      => 'test_transaction',
            'type'          => 'attendee',
            'is_sent'       => false,
            'schedule'      => Carbon::now()->addDay(-1)
                                     ->format('d M Y H:i'),
        ];

        $uri = route('events.checkinRecords.notification', $event);

        $this->post($uri, $data);

        $notification = Notification::first();

        Artisan::call('notification:schedule');

        Queue::assertPushedOn('email', ScheduleNotification::class);
        Queue::assertPushed(ScheduleNotification::class, $number);
    }
}
