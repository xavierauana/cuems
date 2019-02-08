<?php

use Faker\Generator as Faker;

$factory->define(App\Transaction::class, function (Faker $faker) {
    $transactionStatusValues = array_values(\App\Enums\TransactionStatus::getStatus());

    return [
        'ticket_id'           => function () {
            return factory(\App\Ticket::class)->create()->id;
        },
        'transaction_type_id' => function () {
            return factory(\App\TransactionType::class)->create()->id;
        },
        'payee_id'            => function () {
            return factory(\App\Delegate::class)->create()->id;
        },
        'payee_type'          => function () {
            return \App\Delegate::class;
        },
        'status'              => $transactionStatusValues[rand(0,
            count($transactionStatusValues) - 1)],
    ];
});
