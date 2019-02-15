<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Setup\EventFactory;
use Tests\TestCase;

class SponsorFilterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function fetch_all_delegates_by_sponsor() {

        $this->singIn();

        /** @var EventFactory $factory */
        $factory = app(EventFactory::class);

        $event = $factory->setSponsorCount(2)
                         ->setSponsoredDelegateCount([2, 3], [0, 1])
                         ->create();

        $response = $this->get('events/' . $event->id . '/sponsors/' . $event->sponsors[0]->id . '/delegates');

        $response->assertViewIs('admin.events.sponsors.delegates');

        $sponsor = $response->getOriginalContent()->getData()['sponsor'];

        $this->assertEquals(2, $sponsor->delegates->count());

        $response = $this->get('events/' . $event->id . '/sponsors/' . $event->sponsors[1]->id . '/delegates');

        $response->assertViewIs('admin.events.sponsors.delegates');

        $sponsor = $response->getOriginalContent()->getData()['sponsor'];

        $this->assertEquals(3, $sponsor->delegates->count());

    }

    /**
     * @test
     */
    public function download_all_delegates_by_sponsor() {
        $this->withoutExceptionHandling();
        $this->singIn();

        /** @var EventFactory $factory */
        $factory = app(EventFactory::class);

        $event = $factory->setSponsorCount(2)
                         ->setSponsoredDelegateCount([2, 3], [0, 1])
                         ->create();

        $response = $this->get('events/' . $event->id . '/sponsors/export');

        $response->assertOk();
    }
}
