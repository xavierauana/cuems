<?php

use Faker\Generator as Faker;

$factory->define(App\AdvertisementType::class, function (Faker $faker) {
    return [
        'name' => $faker->word
    ];
});
