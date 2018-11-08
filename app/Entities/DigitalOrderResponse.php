<?php
/**
 * Author: Xavier Au
 * Date: 8/11/2018
 * Time: 12:04 PM
 */

namespace App\Entities;


class DigitalOrderResponse
{

    public $token = null;

    /**
     * DigitalOrderRequest constructor.
     * @param string $token
     */
    public function __construct(string $token) {
        $this->token = $token;
    }


}
