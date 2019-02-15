<?php

namespace Tests\Feature;

use App\Delegate;
use App\DelegateRole;
use App\Event;
use App\Session;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ShowPagesController extends TestCase
{

    use DatabaseMigrations;

    private $event;
    private $user;

    protected function setUp() {
        parent::setUp();

        $this->event = factory(Event::class)->create();
        $this->user = factory(User::class)->create();

    }

    /**
     * @test
     */
    public function show_event_index_page() {
        $this->actingAs($this->user);

        $response = $this->get(route('events.index'));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function show_event_edit_page() {
        $this->actingAs($this->user);

        $response = $this->get(route('events.edit', $this->event));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function archive_event() {
        $this->actingAs($this->user);

        $response = $this->delete(route('events.destroy', $this->event));

        $response->assertJson([
            'status' => 'completed'
        ]);
    }

    /**
     * @test
     */
    public function show_event_dashboard_page() {
        $this->actingAs($this->user);

        $response = $this->get(route('events.details', $this->event));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function show_event_session_page() {
        $this->actingAs($this->user);

        $response = $this->get(route('events.sessions.index', $this->event));

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function show_event_session_edit_page() {
        $this->actingAs($this->user);
        $delegateRole = factory(DelegateRole::class)->create([
            'label' => 'test'
        ]);


        $session = factory(Session::class)->create([
            'event_id' => $this->event->id
        ]);

        $moderator = factory(Delegate::class)->create([
            'event_id' => $this->event->id
        ]);

        $moderator->roles()->save($delegateRole);

        DB::table('moderators')->insert([
            'session_id'  => $session->id,
            'delegate_id' => $moderator->id
        ]);

        $response = $this->get(route('events.sessions.edit',
            [$this->event, $session->id]));

        $response->assertStatus(200);
    }
}
