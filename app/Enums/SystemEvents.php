<?php
/**
 * Author: Xavier Au
 * Date: 13/10/2018
 * Time: 4:31 PM
 */

namespace App\Enums;


class SystemEvents
{
    // Fired in controllers PaymentController and DelegatesController
    const CREATE_DELEGATE       = 1;
    const ADMIN_CREATE_DELEGATE = 2;

    // Fired in controllers CheckInController
    const CHECK_IN = 50;

    // Transactions fired in Transaction Model Observer \App\Observers\TransactionObserver
    const TRANSACTION_COMPLETED = 91;
    const TRANSACTION_REFUND    = 92;
    const TRANSACTION_PENDING   = 93;
    const TRANSACTION_FAILED    = 94;
    const TRANSACTION_VOID      = 95;

    public static function getEvents(): array {
        return (new \ReflectionClass(static::class))->getConstants();
    }
}