<?php

use Faker\Generator as Faker;

$factory->define(App\Advertisement::class, function (Faker $faker) {
    return [
        'buyer'   => $faker->company,
        'type_id' => function () {
            return factory(\App\AdvertisementType::class)->create()->id;
        },
    ];
});
