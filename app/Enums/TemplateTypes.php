<?php
/**
 * Author: Xavier Au
 * Date: 13/10/2018
 * Time: 4:31 PM
 */

namespace App\Enums;


class TemplateTypes
{
    const EMAIL  = 1;
    const TICKET = 2;
    const SMS    = 3;

    public static function getTypes(): array {
        return (new \ReflectionClass(static::class))->getConstants();
    }
}