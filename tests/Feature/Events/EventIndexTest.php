<?php

namespace Tests\Feature\Events;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class EventIndexTest extends TestCase
{
    use DatabaseMigrations;

    private $user;

    /**
     *
     */
    protected function setUp() {

        parent::setUp(); // TODO: Change the autogenerated stub

        $this->user = factory(User::class)->create();

        Auth::login($this->user);

    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_get_index_page() {

        //        factory(Event::class, 5)->create();

        $response = $this->get(route("events.index"));

        $response->assertViewIs("admin.events.index");

    }
}
