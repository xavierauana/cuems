<?php

namespace Tests;

use App\DelegateRole;
use App\Enums\TransactionStatus;
use App\Event;
use App\Sponsor;
use App\Ticket;
use App\TransactionType;
use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Feature\ManageDelegateTest;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function singIn(): User {
        $user = factory(User::class)->create();

        $this->actingAs($user);

        return $user;
    }

    protected function createData(
        Event $event = null, int $status = TransactionStatus::AUTHORIZED
    ): array {

        $event = $event ?? factory(Event::class)->create();
        $ticket = factory(Ticket::class)->create([
            'event_id' => $event->id
        ]);
        $transactionType = factory(TransactionType::class)->create();

        factory(DelegateRole::class)->create([
            'is_default' => true
        ]);

        return [
            "prefix"                        => $this->faker->title,
            "first_name"                    => $this->faker->firstName,
            "last_name"                     => $this->faker->lastName,
            "is_male"                       => rand(0, 1),
            "position"                      => $this->faker->jobTitle,
            "department"                    => $this->faker->jobTitle,
            "institution"                   => $this->faker->company,
            "other_institution"             => $this->faker->company,
            "address_1"                     => $this->faker->address,
            "address_2"                     => $this->faker->address,
            "address_3"                     => $this->faker->address,
            "email"                         => $this->faker->email,
            "mobile"                        => $this->faker->phoneNumber,
            "fax"                           => $this->faker->phoneNumber,
            "country"                       => $this->faker->country,
            "training_organisation"         => $this->faker->company,
            "training_other_organisation"   => $this->faker->company,
            "training_organisation_address" => $this->faker->address,
            "supervisor"                    => $this->faker->name,
            "training_position"             => $this->faker->jobTitle,
            "is_duplicated"                 => "NO",
            "is_verified"                   => rand(0, 1),
            "roles_id"                      => null,
            "duplicated_with"               => null,
            "sponsor"                       => [
                "email"      => $this->faker->email,
                "name"       => $this->faker->name,
                "address"    => $this->faker->address,
                "sponsor_id" => factory(Sponsor::class)->create([
                    'event_id' => $event->id
                ])->id,
            ],
            "status"                        => $status,
            "ticket_id"                     => $ticket->id,
            "note"                          => $this->faker->paragraph,
            "transaction_type_id"           => $transactionType->id,
        ];

    }
}
