<?php

use Faker\Generator as Faker;

$factory->define(App\DelegateRole::class, function (Faker $faker) {
    return [
        'label' => $faker->word,
        'code'  => $faker->uuid
    ];
});
