<?php

namespace Tests\Unit;

use App\Delegate;
use App\Enums\TransactionStatus;
use App\Event;
use App\Events\SystemEvent;
use App\Ticket;
use App\Transaction;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class TransactionSystemEventsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_transaction_completed() {
        $event = factory(Event::class)->create();

        $delegate = factory(Delegate::class)->create([
            'event_id' => $event->id
        ]);
        $ticket = factory(Ticket::class)->create([
            'event_id' => $event->id
        ]);

        $this->expectsEvents(SystemEvent::class);

        factory(Transaction::class)->create([
            'payee_id'   => $delegate->id,
            'payee_type' => get_class($delegate),
            'ticket_id'  => $ticket->id,
            'status'     => TransactionStatus::COMPLETED
        ]);

    }
}
