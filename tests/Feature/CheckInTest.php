<?php

namespace Tests\Feature;

use App\Delegate;
use App\DelegateRole;
use App\Ticket;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckInTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function sent_token_to_get_user_data() {
        $this->withoutExceptionHandling();
        $this->actingAs(factory(\App\User::class)->create());
        factory(Delegate::class)->create();
        $delegate = factory(Delegate::class)->create();
        $ticket = factory(Ticket::class)->create([
            'event_id' => $delegate->event->id
        ]);

        factory(Transaction::class)->create([
            'payee_type' => get_class($delegate),
            'payee_id'   => $delegate->id,
            'ticket_id'  => $ticket->id
        ]);

        $token = $delegate->transactions->first()->uuid;
        $uri = route('events.checkin.getDelegate',
            [$delegate->event->id, $token]);
        $response = $this->json("GET", $uri);

        $expectedJsonData = [
            'event'  => $delegate->event->title,
            'delegate'  => [
                "name"        => $delegate->name,
                "institution" => $delegate->institution,
                "position"    => $delegate->position,
                "department"  => $delegate->department
            ],
            'ticket'    => ($transaction = $delegate->transactions->first()) ? $transaction->ticket->name : null,
            'check_in'  => [],
        ];
        $response->assertJson([
            "data" => $expectedJsonData
        ]);

    }

    /**
     * @test
     */
    public function check_in_user() {
        $this->actingAs(factory(\App\User::class)->create());
        factory(Delegate::class)->create();
        $delegate = factory(Delegate::class)->create();
        $ticket = factory(Ticket::class)->create([
            'event_id' => $delegate->event->id
        ]);

        factory(Transaction::class)->create([
            'payee_type' => get_class($delegate),
            'payee_id'   => $delegate->id,
            'ticket_id'  => $ticket->id
        ]);

        $knownDate = Carbon::create(2001, 5, 21, 12);
        Carbon::setTestNow($knownDate);

        $token = $delegate->transactions->first()->uuid;
        $uri = route('events.checkin.delegate', [$delegate->event->id, $token]);
        $this->json("POST", $uri)
             ->assertJson(['status' => 'completed']);

        $this->assertDatabaseHas('check_in', [
            'transaction_id' => $delegate->transactions->first()->id,
            'created_at'     => $knownDate->toDateTimeString()
        ]);

    }

    /**
     * @test
     */
    public function check_in_user_verify() {
        $this->actingAs(factory(\App\User::class)->create());
        factory(Delegate::class)->create();
        $delegate = factory(Delegate::class)->create();
        $ticket = factory(Ticket::class)->create([
            'event_id' => $delegate->event->id
        ]);

        factory(Transaction::class)->create([
            'payee_type' => get_class($delegate),
            'payee_id'   => $delegate->id,
            'ticket_id'  => $ticket->id
        ]);

        $knownDate = Carbon::create(2001, 5, 21, 12);
        Carbon::setTestNow($knownDate);

        $token = $delegate->transactions->first()->uuid;
        $uri = route('events.checkin.delegate', [$delegate->event->id, $token]);
        $this->json("POST", $uri);

        $token = $delegate->transactions->first()->uuid;
        $uri = route('events.checkin.getDelegate',
            [$delegate->event->id, $token]);
        $response = $this->json("GET", $uri);

        $expectedJsonData = [
            'event'  => $delegate->event->title,
            'delegate'  => [
                "name"        => $delegate->name,
                "institution" => $delegate->institution,
                "position"    => $delegate->position,
                "department"  => $delegate->department
            ],
            'ticket'    => ($transaction = $delegate->transactions->first()) ? $transaction->ticket->name : null,
            'check_in'  => [],
        ];
        $response->assertJson([
            "data" => $expectedJsonData
        ]);

    }


}
