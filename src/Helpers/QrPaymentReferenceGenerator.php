<?php

namespace Sprain\SwissQrBill\Helpers;

use byrokrat\checkdigit\Modulo10;

class QrPaymentReferenceGenerator
{
    /** @var string */
    private $customerIdentificationNumber;

    /** @var string */
    private $referenceNumber;

    /** @var Modulo10 */
    private $modulo10;

    public function __construct()
    {
        $this->modulo10 = new Modulo10();
    }

    public function getCustomerIdentificationNumber()
    {
        return $this->customerIdentificationNumber;
    }

    public function setCustomerIdentificationNumber($customerIdentificationNumber)
    {
        $this->customerIdentificationNumber = $customerIdentificationNumber;

        return $this;
    }

    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }

    public function setReferenceNumber($referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;

        return $this;
    }

    public function generate()
    {
        $completeReferenceNumber = $this->getCustomerIdentificationNumber();
        $completeReferenceNumber .= str_pad($this->getReferenceNumber(), 20, '0', STR_PAD_LEFT);
        $completeReferenceNumber .= $this->modulo10($completeReferenceNumber);

        return $completeReferenceNumber;
    }

    private function modulo10($number)
    {
        return $this->modulo10->calculateCheckDigit($number);
    }
}
