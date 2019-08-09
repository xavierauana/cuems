<?php

namespace Tests\Feature;

use App\Delegate;
use App\Event;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PositionGroupingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function get_position_grouping_index() {
        $this->withoutExceptionHandling();

        $event = factory(Event::class)->create();

        $user = factory(User::class)->create();

        $url = route('events.position-groupings.index', $event);

        $this->actingAs($user)
             ->get($url)
             ->assertSuccessful()
             ->assertViewHas('event')
             ->assertViewHas('groupings')
             ->assertViewIs('admin.events.position-groupings.index');
    }

    /**
     * @test
     */
    public function get_delegate_position() {

        $this->withoutExceptionHandling();

        $delegate = factory(Delegate::class)->create();

        $user = factory(User::class)->create();

        $url = route('events.position-groupings.index', $delegate->event);
        $this->actingAs($user)
             ->get($url)
             ->assertSee($delegate->position);
    }
}
