<?php
/**
 * Author: Xavier Au
 * Date: 13/10/2018
 * Time: 4:31 PM
 */

namespace App\Enums;


class TransactionStatus
{
    const PENDING   = 0;
    const COMPLETED = 1;
    const REFUND    = 2;
    const FAILED    = 3;

    public static function getStatus(): array {
        $reflection = new \ReflectionClass(static::class);

        return $reflection->getConstants();
    }
}