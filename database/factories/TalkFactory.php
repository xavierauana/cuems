<?php

use Faker\Generator as Faker;

$factory->define(App\Talk::class, function (Faker $faker) {
    return [
        'title'      => $faker->word(),
        'subtitle'   => $faker->sentence(),
        'session_id' => function () {
            return factory(\App\Session::class)->create()->id;
        }
    ];
});
