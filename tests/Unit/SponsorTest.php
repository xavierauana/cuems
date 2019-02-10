<?php

namespace Tests\Unit;

use App\Event;
use App\Sponsor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class SponsorTest extends TestCase
{
    use RefreshDatabase;


    /**
     * @test
     */
    public function belongs_to_event() {

        $sponsor = factory(Sponsor::class)->create();

        $this->assertInstanceOf(Event::class, $sponsor->event);

    }

    /**
     * @test
     */
    public function has_many_sponsor_records() {

        $sponsor = factory(Sponsor::class)->create();

        $this->assertInstanceOf(Collection::class, $sponsor->records);

    }

    /**
     * Sponsored Delegates
     * @test
     */
    public function has_many_delegates() {
        $sponsor = factory(Sponsor::class)->create();

        $this->assertInstanceOf(Collection::class, $sponsor->delegates);

    }
}
