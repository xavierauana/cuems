<?php

use App\Event;
use Faker\Generator as Faker;

$factory->define(App\Sponsor::class, function (Faker $faker) {
    return [
        'name'     => $faker->name,
        'event_id' => function () {
            return factory(Event::class)->create();
        },
    ];
});
