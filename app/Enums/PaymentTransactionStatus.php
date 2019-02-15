<?php
/**
 * Author: Xavier Au
 * Date: 8/11/2018
 * Time: 12:29 PM
 */

namespace App\Enums;


class PaymentTransactionStatus
{
    const AP = "Approved";
    const RJ = "Rejected";
    const TO = "Timeout";
    const CC = "Cancelled by user";
    const NF = "Not Found";
}