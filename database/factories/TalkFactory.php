<?php

use Faker\Generator as Faker;

$factory->define(App\Talk::class, function (Faker $faker) {
    return [
        'title'      => $faker->words,
        'subtitle'   => $faker->sentences,
        'session_id' => factory(\App\Session::class)->create()->id
    ];
});
