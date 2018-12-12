<?php

namespace Tests\Unit;

use App\Delegate;
use App\DelegateRole;
use App\Enums\SystemEvents;
use App\Enums\TransactionStatus;
use App\Event;
use App\Jobs\SendNotification;
use App\Notification;
use App\Transaction;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\MailCatcherTestCase;

class SystemNotificationsTest extends MailCatcherTestCase
{
    use DatabaseMigrations;

    protected function tearDown() {

        $this->removeAllEmails();

        parent::tearDown();
    }

    public function test_notification_trigger() {
        factory(Notification::class)->create([
            'template' => 'test',
            'event'    => SystemEvents::TRANSACTION_COMPLETED
        ]);

        $delegate = factory(Delegate::class)->create([
            'event_id' => factory(Event::class)->create()->id
        ]);

        $this->expectsJobs([SendNotification::class]);

        factory(Transaction::class)->create([
            "status"     => TransactionStatus::COMPLETED,
            "payee_type" => get_class($delegate),
            "payee_id"   => $delegate->id
        ]);

    }

    public function test_notification_not_trigger() {
        factory(Notification::class)->create([
            'event' => SystemEvents::TRANSACTION_COMPLETED
        ]);

        $this->doesntExpectJobs(SendNotification::class);

        $delegate = factory(Delegate::class)->create([
            'event_id' => factory(Event::class)->create()->id
        ]);
        factory(Transaction::class)->create([
            "status"     => TransactionStatus::PROCESSING,
            "payee_type" => get_class($delegate),
            "payee_id"   => $delegate->id
        ]);
    }

    public function test_notification_with_role_trigger() {

        $role = factory(DelegateRole::class)->create();

        factory(Notification::class)->create([
            'event'   => SystemEvents::TRANSACTION_COMPLETED,
            'role_id' => $role->id
        ]);

        $this->expectsJobs(SendNotification::class);

        $delegate = factory(Delegate::class)->create([
            'event_id' => factory(Event::class)->create()->id
        ]);

        $delegate->roles()->save($role);

        factory(Transaction::class)->create([
            "status"     => TransactionStatus::COMPLETED,
            "payee_type" => get_class($delegate),
            "payee_id"   => $delegate->id
        ]);
    }

    public function test_notification_with_role_not_trigger() {

        $role1 = factory(DelegateRole::class)->create();
        $role2 = factory(DelegateRole::class)->create();

        factory(Notification::class)->create([
            'event'   => SystemEvents::TRANSACTION_COMPLETED,
            'role_id' => $role1->id
        ]);

        $this->doesntExpectJobs(SendNotification::class);

        $delegate = factory(Delegate::class)->create([
            'event_id' => factory(Event::class)->create()->id
        ]);

        $delegate->roles()->save($role2);

        factory(Transaction::class)->create([
            "status"     => TransactionStatus::COMPLETED,
            "payee_type" => get_class($delegate),
            "payee_id"   => $delegate->id
        ]);
    }

    /*
     *
     */
    public function test_notification_email_sent() {

        $from = "xavier";
        $fromEmail = "xavier.au@anacreation.com";
        $subject = "Transaction Created";

        factory(Notification::class)->create([
            'template'   => 'test_transaction',
            'from_name'  => $from,
            'from_email' => $fromEmail,
            'subject'    => $subject,
            'event'      => SystemEvents::TRANSACTION_COMPLETED
        ]);

        $delegate = factory(Delegate::class)->create([
            'event_id' => factory(Event::class)->create()->id
        ]);

        factory(Transaction::class)->create([
            "status"     => TransactionStatus::COMPLETED,
            "payee_type" => get_class($delegate),
            "payee_id"   => $delegate->id
        ]);

        $email = $this->getLastEmail();
        $this->assertEmailBodyContains("testing transaction", $email);
        $this->assertEmailWasSentTo($delegate->email, $email);
        $this->assertEmailWasSentFrom($fromEmail, $email);
        $this->assertEmailSubjectContains($subject, $email);
    }
}
