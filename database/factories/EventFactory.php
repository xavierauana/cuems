<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\Event::class, function (Faker $faker) {
    $start = Carbon::now();
    $end = Carbon::now()->addDays(3);

    return [
        'title'    => $faker->word(),
        'start_at' => $start,
        'end_at'   => $end
    ];
});
