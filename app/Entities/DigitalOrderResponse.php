<?php
/**
 * Author: Xavier Au
 * Date: 8/11/2018
 * Time: 12:04 PM
 */

namespace App\Entities;


class DigitalOrderResponse
{

    public $url = null;
    public $token = null;

    /**
     * DigitalOrderRequest constructor.
     * @param string $token
     * @param string $url
     */
    public function __construct(string $token, string $url) {

        if (empty($token) or empty($url)) {
            throw new \InvalidArgumentException("Argument missing for DigitalOrderResponse instantiation.");
        }
        $this->token = $token;
        $this->url = $url;
    }


}
