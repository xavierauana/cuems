<?php
/**
 * Author: Xavier Au
 * Date: 8/11/2018
 * Time: 11:46 AM
 */

namespace App\Enums;


class PaymentType
{
    const Authorisation  = 1;
    const Capture        = 2;
    const Void_Auth      = 3;
    const Sale           = 7;
    const Void_Sale      = 8;
    const UPOP_Auth      = 41;
    const UPOP_Capture   = 42;
    const UPOP_Void_Auth = 43;
    const UPOP_Sale      = 47;
    const UPOP_Void_Sale = 48;
}