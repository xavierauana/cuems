<?php

use Faker\Generator as Faker;

$factory->define(App\Ticket::class, function (Faker $faker) {

    $startAt = \Carbon\Carbon::now();

    $endAt = \Carbon\Carbon::now()->addDays(random_int(1, 60));

    return [
        'price'    => random_int(100, 10000),
        'name'     => $faker->word,
        'start_at' => $startAt,
        'end_at'   => $endAt,
        'event_id' => factory(\App\Event::class)->create()->id
    ];
});
