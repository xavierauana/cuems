<?php

use Faker\Generator as Faker;

$factory->define(App\Session::class, function (Faker $faker) {
    return [
        'title'    => $faker->word(),
        'event_id' => factory(\App\Event::class)->create()->id,
    ];
});
