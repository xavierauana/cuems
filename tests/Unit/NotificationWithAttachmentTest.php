<?php

namespace Tests\Unit;

use App\Delegate;
use App\Event;
use App\Mail\TransactionMail;
use App\Notification;
use App\Ticket;
use App\Transaction;
use App\UploadFile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\MailCatcherTestCase;

class NotificationWithAttachmentTest extends MailCatcherTestCase
{

    use DatabaseMigrations;

    protected function tearDown() {
        $this->removeAllEmails();

        parent::tearDown(); // TODO: Change the autogenerated stub
    }


    /**
     * @test
     */
    public function notification_has_attachment() {
        $event = factory(Event::class)->create();
        $ticket = factory(Ticket::class)->create([
            'event_id' => $event->id
        ]);
        $delegate = factory(Delegate::class)->create([
            'event_id' => $event->id
        ]);
        $transaction = factory(Transaction::class)->create([
            'payee_type' => get_class($delegate),
            'payee_id'   => $delegate->id,
            'ticket_id'  => $ticket->id
        ]);
        $notification = factory(Notification::class)->create([
            'template' => 'test_transaction',
            'event_id' => $event->id,
        ]);
        $filename = "CROSS入庫通知書.pdf";
        $file = factory(UploadFile::class)->create([
            "name"     => $filename,
            "path"     => "events/1/uploadFiles/{$filename}",
            "event_id" => $event->id
        ]);

        DB::table('notification_upload_file')->insert([
            "notification_id" => $notification->id,
            "upload_file_id"  => $file->id,
        ]);

        Mail::to('xavier.au@anacreation.com')
            ->send(new TransactionMail($notification, $transaction, $event));


        $email = $this->getLastEmail();
        $this->assertEmailHasAttachment($filename, $email);
    }
}