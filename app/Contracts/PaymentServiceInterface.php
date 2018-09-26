<?php
/**
 * Author: Xavier Au
 * Date: 21/9/2018
 * Time: 6:17 PM
 */

namespace App\Contracts;


interface PaymentServiceInterface
{
    public function charge(string $token, int $amount);
}