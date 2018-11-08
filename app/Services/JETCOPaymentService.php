<?php
/**
 * Author: Xavier Au
 * Date: 8/11/2018
 * Time: 12:31 PM
 */

namespace App\Services;


use App\Contracts\PaymentServiceInterface;
use App\Entities\ChargeResponse;
use App\Entities\DigitalOrderRequest;
use App\Entities\DigitalOrderResponse;
use App\Entities\PaymentGatewayEndpoints;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class JETCOPaymentService implements PaymentServiceInterface
{
    private $client;
    private $endPoints;


    /**
     * JETCOPaymentService constructor.
     */
    public function __construct() {

        $this->client = new Client;

        $this->endPoints = new PaymentGatewayEndpoints;

    }

    public function checkPaymentGatewayStatus(): bool {

        $response = $this->client->get($this->endPoints->getServerStatusUrl());

        $object = simplexml_load_string((string)$response->getBody());

        return (string)$object->Status === "Available";

    }

    public function getRedirectUrl(array $params): ?string {
        // TODO: Implement getRedirectUrl() method.
    }

    public function getDigitalOrder(DigitalOrderRequest $request
    ): ?DigitalOrderResponse {

        $httpRequest = new Request("GET",
            $this->endPoints->getRequestDOUrl() .
            "?amount=" . urlencode($request->amount) .
            "&txnType=" . urlencode($request->txnType) .
            "&returnURL=" . urlencode($request->returnURL) .
            "&locale=" . urlencode($request->locale) .
            "&invoiceNumber=" . "U078" . urlencode($request->invoiceNumber)
        );

        $response = $this->client->send($httpRequest);

        dd((string)$response->getBody());
    }

    /**
     * @param string $token
     * @param int    $amount
     * @return \App\Entities\ChargeResponse
     */
    public function charge(string $token, int $amount): ChargeResponse {
        // TODO: Implement charge() method.
    }
}