<?php

return [
    'settings'       => [
        'important_note',
        'privacy',
        'registration_id_prefix'
    ],
    'payment_prefix' => env('JETCO_PREFIX', null),
    'invoice_prefix' => env('INVOICE_PREFIX', 'test')
];
