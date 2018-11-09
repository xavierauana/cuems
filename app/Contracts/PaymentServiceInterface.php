<?php
/**
 * Author: Xavier Au
 * Date: 21/9/2018
 * Time: 6:17 PM
 */

namespace App\Contracts;


use App\Entities\ChargeResponse;
use App\Entities\DigitalOrderRequest;
use App\Entities\DigitalOrderResponse;

interface PaymentServiceInterface
{

    public function checkPaymentGatewayStatus(): bool;

    public function getRedirectUrl(array $params): ?string;

    public function getDigitalOrder(DigitalOrderRequest $request
    ): DigitalOrderResponse;

    /**
     * @param string $token
     * @param int    $amount
     * @return \App\Entities\ChargeResponse
     */
    public function charge(string $token, int $amount): ChargeResponse;
}