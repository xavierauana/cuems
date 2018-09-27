<?php
/**
 * Author: Xavier Au
 * Date: 27/9/2018
 * Time: 8:19 AM
 */

namespace App\Entities;


class ChargeResponse
{

    public $last4;
    public $brand;
    public $chargeID;

    /**
     * ChargeResponse constructor.
     * @param $last4
     * @param $brand
     * @param $chargeID
     */
    public function __construct($chargeID, $brand, $last4) {
        $this->last4 = $last4;
        $this->brand = $brand;
        $this->chargeID = $chargeID;
    }


}