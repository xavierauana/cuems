<?php
/**
 * Author: Xavier Au
 * Date: 8/11/2018
 * Time: 12:04 PM
 */

namespace App\Entities;


use App\Enums\PaymentType;

class DigitalOrderRequest
{
    /**
     * @var string
     */
    public $invoiceNumber;
    /**
     * @var float
     */
    public $amount;
    /**
     * @var int
     */
    public $txnType;
    /**
     * @var string
     */
    public $returnURL;
    /**
     * @var string
     */
    public $locale;


    /**
     * DigitalOrder constructor.
     * @param string $invoiceNumber
     * @param float  $amount
     * @param int    $txnType
     * @param string $returnURL
     * @param string $locale
     */
    public function __construct(
        string $invoiceNumber, float $amount, int $txnType, string $returnURL,
        string $locale = "en_us"
    ) {

        $this->checkTransactionType($txnType);

        $this->checkLocale($locale);

        $this->checkAmount($amount);

        $this->checkInvoiceNumber($invoiceNumber);

        $this->invoiceNumber = $invoiceNumber;
        $this->amount = $amount;
        $this->txnType = $txnType;
        $this->returnURL = $returnURL;
        $this->locale = $locale;
    }

    /**
     * @param int $txnType
     */
    private function checkTransactionType(int $txnType): void {

        $class = new \ReflectionClass(PaymentType::class);

        $availableTypes = array_values($class->getConstants());

        if (!in_array($txnType, $availableTypes)) {
            throw new \InvalidArgumentException("The transaction tye does not recognise for Digital Order");
        }
    }

    private function checkLocale($locale) {
        $availableLocales = [
            'en_us',
            'zh_tr',
            'zh_cn'
        ];

        if (!in_array($locale, $availableLocales)) {
            throw new \InvalidArgumentException("The locale does not recognise for Digital Order");
        }
    }

    private function checkAmount($amount) {

        if ($amount > 9999999999 or $amount < 0) {
            throw new \InvalidArgumentException("The amount acceptable for Digital Order");
        }
    }

    private function checkInvoiceNumber($invoiceNumber) {
        if (strlen($invoiceNumber) > 18) {
            throw new \InvalidArgumentException("The invoice number is too long for Digital Order");
        }
    }
}
