<?php

namespace Tests\Unit;

use App\DelegateRole;
use App\Enums\TransactionStatus;
use App\Event;
use App\Services\DelegateCreationService;
use App\Ticket;
use App\User;
use Faker\Generator;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;


class DelegateCreationServiceTest extends TestCase
{

    use DatabaseMigrations;

    /**
     * @var DelegateCreationService
     */
    private $service;

    private $faker;

    private $event;

    /**
     *
     */
    protected function setUp() {

        parent::setUp();


        $this->faker = app(Generator::class);

        $this->user = factory(User::class)->create();

        $this->event = factory(Event::class)->create();

        factory(DelegateRole::class)->create([
            'is_default' => true
        ]);

        $this->ticket = factory(Ticket::class)->create();

        $this->service = app(DelegateCreationService::class,
            ['user' => $this->user]);
    }


    /**
     * @test
     */
    public function successfully_create_delegate() {

        $data = $this->createDelegateData();

        $newDelegate = $this->service->create($this->event, $data);

        $this->assertDatabaseHas('delegates', [
            'event_id'        => $this->event->id,
            'email'           => $data['email'],
            'id'              => $newDelegate->id,
            'registration_id' => 1,
        ]);
    }

    /**
     * @test
     */
    public function create_2_delegates_with_incremental_registration_id() {

        $data1 = $this->createDelegateData();
        $data2 = $this->createDelegateData();

        $newDelegate1 = $this->service->create($this->event, $data1);
        $newDelegate2 = $this->service->create($this->event, $data2);

        $this->assertDatabaseHas('delegates', [
            'event_id'        => $this->event->id,
            'email'           => $data1['email'],
            'id'              => $newDelegate1->id,
            'registration_id' => 1,
        ]);
        $this->assertDatabaseHas('delegates', [
            'event_id'        => $this->event->id,
            'email'           => $data2['email'],
            'id'              => $newDelegate2->id,
            'registration_id' => $newDelegate1->registration_id + 1,
        ]);
    }

    /**
     * @return array
     */
    private function createDelegateData(): array {
        $data = [
            'prefix'      => $this->faker->title,
            'first_name'  => $this->faker->firstName,
            'last_name'   => $this->faker->lastName,
            'is_male'     => 1,
            'email'       => $this->faker->companyEmail,
            'mobile'      => $this->faker->phoneNumber,
            'position'    => $this->faker->jobTitle,
            'department'  => "Department",
            'institution' => $this->faker->company,
            'address_1'   => $this->faker->address,
            'address_2'   => $this->faker->address,
            'address_3'   => $this->faker->address,
            'country'     => $this->faker->country,
            'ticket_id'   => $this->ticket->id,
            'status'      => TransactionStatus::COMPLETED
        ];

        return $data;
    }
}
