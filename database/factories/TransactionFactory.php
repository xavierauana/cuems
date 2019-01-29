<?php

use Faker\Generator as Faker;

$factory->define(App\Transaction::class, function (Faker $faker) {
    $transactionStatusValues = array_values(\App\Enums\TransactionStatus::getStatus());

    return [
        'status' => $transactionStatusValues[rand(0,
            count($transactionStatusValues) - 1)],
    ];
});
