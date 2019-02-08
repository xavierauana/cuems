<?php

use Faker\Generator as Faker;

$factory->define(App\TransactionType::class, function (Faker $faker) {
    return [
        'label' => $faker->word
    ];
});
