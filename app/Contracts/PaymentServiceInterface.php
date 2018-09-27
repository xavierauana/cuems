<?php
/**
 * Author: Xavier Au
 * Date: 21/9/2018
 * Time: 6:17 PM
 */

namespace App\Contracts;


use App\Entities\ChargeResponse;

interface PaymentServiceInterface
{
    /**
     * @param string $token
     * @param int    $amount
     * @return \App\Entities\ChargeResponse
     */
    public function charge(string $token, int $amount): ChargeResponse;
}