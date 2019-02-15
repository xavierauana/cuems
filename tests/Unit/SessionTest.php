<?php

namespace Tests\Unit;

use App\Event;
use App\Session;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SessionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function belongs_to_event() {
        $session = factory(Session::class)->create();

        $this->assertInstanceOf(Event::class, $session->event);
    }
}
