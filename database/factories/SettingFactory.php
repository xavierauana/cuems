<?php

use Faker\Generator as Faker;

$factory->define(App\Setting::class, function (Faker $faker) {
    return [
        "key"      => $faker->word,
        "value"    => $faker->sentence,
        'event_id' => factory(\App\Event::class)->create()->id
    ];
});
