<?php
/**
 * Author: Xavier Au
 * Date: 4/12/2018
 * Time: 1:50 PM
 */

namespace App\Enums;


class DelegateDuplicationStatus
{
    const UNKNOWN    = "UNKNOWN";
    const DUPLICATED = "DUPLICATED";
    const NO         = "NO";

    public static function getStatus(): array {
        $reflection = new \ReflectionClass(static::class);

        return $reflection->getConstants();
    }
}