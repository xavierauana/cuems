<?php

namespace Tests\Feature;

use App\Enums\SessionModerationType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Setup\SessionFactory;
use Tests\TestCase;

class ManageSessionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function api_get_sessions() {

        /** @var SessionFactory $service */
        $service = app(SessionFactory::class);
        $session1 = $service->setModeratorType(SessionModerationType::CHAIRPERSON)
                            ->create();

        $uri = "/api/events/{$session1->event->id}/sessions";

        $response = $this->json('get', $uri);

        $response->assertOk();

    }

    /**
     * @test
     */
    public function get_order_sessions() {

        /** @var SessionFactory $service */
        $session1 = app(SessionFactory::class)
            ->setOrder(2)
            ->setModeratorType(SessionModerationType::CHAIRPERSON)
            ->create();

        $session2 = app(SessionFactory::class)
            ->setEvent($session1->event)
            ->setOrder(1)
            ->setModeratorType(SessionModerationType::MODERATOR)
            ->create();

        $uri = "/api/events/{$session1->event->id}/sessions";

        $response = $this->json('get', $uri);

        $json = json_decode($response->getContent(), true);

        $this->assertEquals(2, count($json['data']));
        $this->assertEquals($session2->id, $json['data'][0]['id']);
        $this->assertEquals($session1->id, $json['data'][1]['id']);

    }
}
