<?php

namespace Sprain\SwissQrBill\PaymentPart\Output;

use Sprain\SwissQrBill\QrBill;

abstract class AbstractOutput
{
    /** @var QrBill */
    protected $qrBill;

    /** @var  string */
    protected $language;

    public function __construct(QrBill $qrBill, string $language)
    {
        $this->qrBill = $qrBill;
        $this->language = $language;
    }

    public function getQrBill() : ?QrBill
    {
        return $this->qrBill;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getInformationElements() : array
    {
        $informationElements = [];

        $availableInformationElements =  [
            'text.creditor' => $this->qrBill->getCreditorInformation()->getIban() . "\n" . $this->qrBill->getCreditor()->getFullAddress(),
            'text.reference' => $this->qrBill->getPaymentReference()->getReference(),
            'text.additionalInformation' => $this->qrBill->getAdditionalInformation()->getMessage(),
            'text.payableBy' => $this->qrBill->getUltimateDebtor() ? $this->qrBill->getUltimateDebtor()->getFullAddress() : null,
        ];

        foreach($availableInformationElements as $key => $content) {
            if ($content) {
                $informationElements[$key] = $content;
            }
        }

        return $informationElements;
    }

    public function getInformationElementsOfReceipt() : array
    {
        $informationElements = $this->getInformationElements();
        unset($informationElements['text.additionalInformation']);

        return $informationElements;
    }
}