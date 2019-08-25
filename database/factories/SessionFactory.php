<?php

use Faker\Generator as Faker;

$factory->define(App\Session::class, function (Faker $faker) {
    return [
        'title'           => $faker->word(),
        'event_id'        => function () {
            return factory(\App\Event::class)->create()->id;
        },
        'order'           => 1,
        'moderation_type' => 1,
    ];
});
