<?php
/**
 * Author: Xavier Au
 * Date: 21/9/2018
 * Time: 6:20 PM
 */

namespace App\Services;


use App\Contracts\PaymentServiceInterface;
use App\Entities\ChargeResponse;
use Stripe\Charge;
use Stripe\Stripe;

class StripeService implements PaymentServiceInterface
{
    /**
     * @var string
     */
    private $currency;


    /**
     * StripeService constructor.
     * @param string $currency
     */
    public function __construct(string $currency = "hkd") {
        $this->currency = $currency;
    }

    /**
     * @param string $token
     */
    public function charge(string $token, int $amount): ChargeResponse {
        Stripe::setApiKey(env("STRIPE_SECRET_KEY"));

        $charge = Charge::create([
            'amount'   => $amount * 100,
            'currency' => $this->currency,
            'source'   => $token
        ]);

        return new ChargeResponse($charge->id, $charge->source->brand,
            $charge->source->last4);
    }
}