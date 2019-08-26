<?php

namespace Tests\Feature;

use App\AdvertisementType;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvertisementTypeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function create_type() {

        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->actingAs($user);

        $uri = route('advertisement_types.store');

        $data = [
            'name' => 'new advertisement type'
        ];

        $response = $this->post($uri, $data);

        $response->assertRedirect(route('advertisement_types.index'));

        $this->assertDatabaseHas('advertisement_types', [
            'name' => $data['name']
        ]);

    }

    /**
     * @test
     */
    public function update_type() {

        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->actingAs($user);

        $type = factory(AdvertisementType::class)->create();

        $uri = route('advertisement_types.update', $type);

        $data = [
            'name' => 'new advertisement type'
        ];

        $response = $this->put($uri, $data);

        $response->assertRedirect(route('advertisement_types.index'));

        $this->assertDatabaseHas('advertisement_types', [
            'name' => $data['name']
        ]);

    }

    /**
     * @test
     */
    public function delete_type() {

        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->actingAs($user);

        $type = factory(AdvertisementType::class)->create();

        $uri = route('advertisement_types.destroy', $type);

        $response = $this->delete($uri);

        $response->assertRedirect(route('advertisement_types.index'));

        $this->assertDatabaseMissing('advertisement_types', [
            'id' => $type->id
        ]);
    }

    /**
     * @test
     */
    public function get_views() {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->actingAs($user);

        $type = factory(AdvertisementType::class)->create();

        $endpoints = [
            [
                'uri'  => "advertisement_types",
                'view' => 'admin.advertisement_types.index',
                'keys' => ['advertisementTypes']
            ],
            [
                'uri'  => "advertisement_types/create",
                'view' => 'admin.advertisement_types.create',
                'keys' => []
            ],
            [
                'uri'  => "advertisement_types/%s/edit",
                'view' => 'admin.advertisement_types.edit',
                'keys' => ['advertisementType']
            ],
        ];

        foreach ($endpoints as $set) {
            $uri = url(sprintf($set['uri'], $type->id));

            dump($uri);

            $response = $this->get($uri);

            $response->assertOk()
                     ->assertViewIs($set['view'])
                     ->assertViewHasAll($set['keys']);
        }

    }
}
