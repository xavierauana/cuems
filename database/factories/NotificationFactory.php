<?php

use Faker\Generator as Faker;

$factory->define(App\Notification::class, function (Faker $faker) {
    $systemEventsValues = array_values(\App\Enums\SystemEvents::getEvents());

    return [
        'template'  => $faker->word,
        'name'      => $faker->word,
        'from_name' => $faker->name,
        'from_email' => $faker->companyEmail,
        'subject'   => $faker->sentence(),
        'event'     => $systemEventsValues[random_int(0,
            count($systemEventsValues) - 1)],
        'event_id'  => factory(\App\Event::class)->create()->id,
    ];
});
