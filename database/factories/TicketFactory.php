<?php

use Faker\Generator as Faker;

$factory->define(App\Ticket::class, function (Faker $faker) {

    $startAt = \Carbon\Carbon::now();

    $endAt = \Carbon\Carbon::now()->addDays(random_int(1, 60));

    return [
        'price'    => random_int(100, 10000),
        'start_at' => $startAt,
        'end_at'   => $endAt,
        'vacancy'  => random_int(1, 100),
        'event_id' => factory(\App\Event::class)->create()->id
    ];
});
