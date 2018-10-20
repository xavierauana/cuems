<?php

use Faker\Generator as Faker;

$factory->define(App\Delegate::class, function (Faker $faker) {
    return [
        'prefix'      => $faker->title,
        'first_name'  => $faker->firstName,
        'last_name'   => $faker->lastName,
        'is_male'     => 1,
        'position'    => $faker->jobTitle,
        'department'  => "Department",
        'institution' => $faker->company,
        'address'     => $faker->address,
        'email'       => $faker->companyEmail,
        'mobile'      => $faker->phoneNumber,
    ];
});
