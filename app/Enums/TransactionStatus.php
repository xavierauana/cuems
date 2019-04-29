<?php
/**
 * Author: Xavier Au
 * Date: 13/10/2018
 * Time: 4:31 PM
 */

namespace App\Enums;


class TransactionStatus
{
    const PROCESSING = 0;
    const COMPLETED  = 1;
    const REFUNDED   = 2;
    const FAILED     = 3;
    const AUTHORIZED = 4;
    const VOID       = 5;

    public static function getStatus(): array {
        $reflection = new \ReflectionClass(static::class);

        return $reflection->getConstants();
    }

    public static function getStatusKey($val): ?string {
        return array_flip(static::getStatus())[$val] ?? null;
    }
}