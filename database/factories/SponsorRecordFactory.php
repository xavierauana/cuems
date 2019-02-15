<?php

use App\Delegate;
use Faker\Generator as Faker;

$factory->define(App\SponsorRecord::class, function (Faker $faker) {
    return [
        'delegate_id' => function () {
            return factory(Delegate::class)->create()->id;
        },
        'sponsor_id'  => function () {
            return factory(\App\Sponsor::class)->create()->id;
        },
    ];
});
