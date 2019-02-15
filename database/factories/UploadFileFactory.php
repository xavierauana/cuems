<?php

use Faker\Generator as Faker;

$factory->define(App\UploadFile::class, function (Faker $faker) {
    $fileName = $faker->name . ".pdf";;

    return [
        'name'     => $fileName,
        'path'     => storage_path("app/events/" . ($event = factory(\App\Event::class)->create())->id . "/uploadFiles/" . $fileName),
        'disk'     => "local",
        'event_id' => $event->id
    ];
});
