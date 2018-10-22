<?php
/**
 * Author: Xavier Au
 * Date: 13/10/2018
 * Time: 4:31 PM
 */

namespace App\Enums;


class SessionModerationType
{
    // Fired in controllers PaymentController and DelegatesController
    const CHAIRPERSON = 1;
    const MODERATOR   = 2;

    public static function getTypes(): array {
        return (new \ReflectionClass(static::class))->getConstants();
    }
}