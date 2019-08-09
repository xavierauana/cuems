<?php

namespace Tests\Feature;

use App\Delegate;
use App\DelegateRole;
use App\Enums\TransactionStatus;
use App\Event;
use App\Sponsor;
use App\Ticket;
use App\TransactionType;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManageDelegateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function create_delegate() {
        $this->actingAs(factory(User::class)->create());

        $event = factory(Event::class)->create();

        $data = $this->createData($event);

        $uri = route('events.delegates.store', $event->id);

        $this->post($uri, $data);

        $this->assertDatabaseHas('delegates', [
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
        ]);

        $delegate = Delegate::whereFirstName($data['first_name'])->first();

        $this->assertEquals(TransactionType::first()->id,
            $delegate->transactions()->first()->transactionType->id);

    }

    /**
     * @test
     */
    public function create_delegate_with_other_role() {

        $this->withoutExceptionHandling();

        $this->actingAs(factory(User::class)->create());

        $event = factory(Event::class)->create();

        $role = factory(DelegateRole::class)->create();

        $data = $this->createData($event);

        $data['roles_id'] = [
            $role->id
        ];

        $uri = route('events.delegates.store', $event->id);

        $this->post($uri, $data);

        $delegate = Delegate::where(
            [
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
            ]
        )->first();

        $this->assertEquals($role->id, $delegate->roles->first()->id);

    }

    /**
     * @test
     */
    public function multiple_roles() {

        $this->actingAs(factory(User::class)->create());

        $event = factory(Event::class)->create();

        $role1 = factory(DelegateRole::class)->create();
        $role2 = factory(DelegateRole::class)->create();

        $data = $this->createData($event);

        $data['roles_id'] = [
            $role1->id,
            $role2->id
        ];

        $uri = route('events.delegates.store', $event->id);

        $this->post($uri, $data);

        $delegate = Delegate::where(
            [
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
            ]
        )->first();

        $this->assertEquals(2, $delegate->roles->count());

        $delegate->roles->each(function (DelegateRole $role) use ($role1, $role2
        ) {
            $this->assertTrue(in_array($role->id, [$role1->id, $role2->id]));
        });

    }

    /**
     * @test
     */
    public function requiredFields() {

        $this->actingAs(factory(User::class)->create());

        $event = factory(Event::class)->create();

        $data = $this->createData($event);

        $requiredFields = [
            'transaction_type_id',
            'first_name',
            'last_name',
        ];

        $uri = route('events.delegates.store', $event->id);

        foreach ($requiredFields as $requiredField) {
            $data[$requiredField] = null;
        }

        $this->post($uri, $data)->assertSessionHasErrors($requiredFields);

    }

    /**
     * @test
     */
    public function delegate_with_other_institution() {

        $this->actingAs(factory(User::class)->create());

        $event = factory(Event::class)->create();

        $data = $this->createData($event);

        $data['other_institution'] = "testing institute";

        $uri = route('events.delegates.store', $event->id);

        $this->post($uri, $data);

        $delegate = Delegate::where(
            [
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
            ]
        )->first();

        $this->assertEquals($data['other_institution'],
            $delegate->institution);
    }
    /**
     * @test
     */
    public function delegate_with_other_position() {
$this->withoutExceptionHandling();
        $this->actingAs(factory(User::class)->create());

        $event = factory(Event::class)->create();

        $data = $this->createData($event);

        $data['position'] = "Others";
        $data['other_position'] = "other position";

        $uri = route('events.delegates.store', $event->id);

        $this->post($uri, $data);

        $delegate = Delegate::where(
            [
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
            ]
        )->first();

        $this->assertEquals($data['other_position'],
            $delegate->position);
    }

    /**
     * @test
     */
    public function create_trainee() {
        $this->actingAs(factory(User::class)->create());

        $event = factory(Event::class)->create();

        $ticket = factory(Ticket::class)->create([
            'event_id' => $event->id,
            'note'     => 'trainee'
        ]);

        $data = $this->createData($event);

        $data['ticket_id'] = $ticket->id;

        $uri = route('events.delegates.store', $event->id);

        $this->post($uri, $data);

        $delegate = Delegate::where(
            [
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
            ]
        )->first();

        $this->assertEquals($data['training_other_organisation'],
            $delegate->training_organisation);
    }

    /**
     * @test
     */
    public function update_delegate_institution_with_others() {
        $this->actingAs(factory(User::class)->create());

        $event = factory(Event::class)->create();

        $data = $this->createData($event);

        $data['other_institution'] = "original institute";

        $uri = route('events.delegates.store', $event->id);

        $this->post($uri, $data);

        $delegate = Delegate::where(
            [
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
            ]
        )->first();

        $data['institution'] = "Others";
        $data['other_institution'] = "brand new institute";

        $uri = route('events.delegates.update', [$event->id, $delegate->id]);

        $this->put($uri, $data);

        $this->assertEquals($data['other_institution'],
            $delegate->refresh()->institution);

        $uri = route('events.delegates.edit', [$event->id, $delegate->id]);

        $this->get($uri, $data)->assertSee($data['other_institution']);

    }

    /**
     * @test
     */
    public function update_delegate_institution_with_position() {
        $this->actingAs(factory(User::class)->create());

        $event = factory(Event::class)->create();

        $data = $this->createData($event);

        $data['position'] = "original position";

        $uri = route('events.delegates.store', $event->id);

        $this->post($uri, $data);

        $delegate = Delegate::where(
            [
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
            ]
        )->first();

        $data['position'] = "Others";
        $data['other_position'] = "brand new position";

        $uri = route('events.delegates.update', [$event->id, $delegate->id]);

        $this->put($uri, $data);

        $this->assertEquals($data['other_position'],
            $delegate->refresh()->position);

        $uri = route('events.delegates.edit', [$event->id, $delegate->id]);

        $this->get($uri, $data)->assertSee($data['other_position']);

    }

}
