<?php

use Faker\Generator as Faker;

$factory->define(App\Delegate::class, function (Faker $faker) {
    return [
        'prefix'      => $faker->title,
        'first_name'  => $faker->firstName,
        'last_name'   => $faker->lastName,
        'is_male'     => 1,
        'email'       => $faker->companyEmail,
        'mobile'      => $faker->phoneNumber,
        'position'    => $faker->jobTitle,
        'department'  => "Department",
        'institution' => $faker->company,
        'address_1'   => $faker->address,
        'address_2'   => $faker->address,
        'address_3'   => $faker->address,
        'country'     => $faker->country,
    ];
});
