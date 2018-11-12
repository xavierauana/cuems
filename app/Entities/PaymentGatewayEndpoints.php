<?php
/**
 * Author: Xavier Au
 * Date: 8/11/2018
 * Time: 12:18 PM
 */

namespace App\Entities;


class PaymentGatewayEndpoints
{

    private $baseUrl;

    /**
     * PaymentGatewayEndpoints constructor.
     * @param $baseUrl
     */
    public function __construct() {

        $this->baseUrl = env("APP_ENV") === "production" ? "https://money.bur.cuhk.edu.hk/CU-IPG" : "http://epaydev.itsc.cuhk.edu.hk:8080/CU-IPG/UAT";
    }

    public function getServerStatusUrl(): string {
        return "{$this->baseUrl}/status.jsp";
    }

    public function getDigitalVoidsUrl(): string {
        return "{$this->baseUrl}/processDV.jsp";
    }

    public function getCaptureUrl(): string {
        return "{$this->baseUrl}/processDC.jsp";
    }

    public function getEnquireUrl(array $params): string {

        if (!isset($params['invoiceNumber']) and !isset($params['DR'])) {
            throw new \InvalidArgumentException("Missing DR and invoice number.");
        }

        $baseUrl = "{$this->baseUrl}/getDR.jsp";

        if (isset($params['DR'])) {
            $url = $baseUrl . "?DR=" . urlencode($params['DR']);
        } else {
            $url = $baseUrl . "?invoiceNumber=" . $params['invoiceNumber'];
        }

        return $url;
    }

    public function getRequestDOUrl(): string {
        return "{$this->baseUrl}/getDO.jsp";
    }
}