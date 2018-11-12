<?php
/**
 * Author: Xavier Au
 * Date: 12/11/2018
 * Time: 10:31 AM
 */

namespace App\Enums;


class PaymentRecordStatus
{
    const CREATED    = "created";
    const REQUEST    = "requested";
    const PROCESSING = "processing";
    const FAILED     = "failed";
    const AUTHORIZED = "authorized";
    const VOID       = "void";
    const CAPTURED   = "captured";
    const REFUNDED   = "refunded";
}