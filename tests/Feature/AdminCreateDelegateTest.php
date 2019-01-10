<?php

namespace Tests\Feature;

use App\Event;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AdminCreateDelegateTest extends TestCase
{
    use DatabaseMigrations;

    private $admin;
    private $event;

    protected function setUp() {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->admin = factory(User::class)->create();
        $this->event = factory(Event::class)->create();

        $this->actingAs($this->admin);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test() {
        $response = $this->get('/events/' . $this->event->id . '/delegates/create');
        $this->type('Mr', 'prefix');
        $this->type('1', 'is_male');
        $this->type('Au', 'last_name');
        $this->type('Xavier', 'first_name');
        $this->type('address 1', 'address_1');
        $this->type('address 2', 'address_2');
        $this->type('address 3', 'address_3');
        $this->type('xavier.au@gmail.com', 'email');
        $this->type('66218556', 'mobile');
        $this->type('IT', 'department');
        $this->type('1', 'ticket_id');
        $this->select('1', 'roles_id[]');
        $this->press('Submit');
        $this->seePageIs('/events/' . $this->event->id . '/delegates');
        $this->visit('/events/' . $this->event->id . '/delegates');
    }
}
