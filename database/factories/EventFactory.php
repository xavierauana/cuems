<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\Event::class, function (Faker $faker) {
    return [
        'title'    => $faker->words(),
        'start_at' => Carbon::now()->toDateTimeString(),
        'end_at'   => Carbon::now()->addDays(3)->toDateTimeString()
    ];
});
