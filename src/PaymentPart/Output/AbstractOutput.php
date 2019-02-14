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

    protected function getInformationElements() : array
    {
        $informationElements = [];

        $availableInformationElements =  [
            'text.creditor' => $this->qrBill->getCreditorInformation()->getFormattedIban() . "\n" . $this->qrBill->getCreditor()->getFullAddress(),
            'text.reference' => $this->qrBill->getPaymentReference()->getFormattedReference(),
            'text.additionalInformation' => $this->qrBill->getAdditionalInformation() ? $this->qrBill->getAdditionalInformation()->getMessage() : null,
            'text.payableBy' => $this->qrBill->getUltimateDebtor() ? $this->qrBill->getUltimateDebtor()->getFullAddress() : null,
        ];

        foreach($availableInformationElements as $key => $content) {
            if ($content) {
                $informationElements[$key] = $content;
            }
        }

        return $informationElements;
    }

    protected function getInformationElementsOfReceipt() : array
    {
        $informationElements = $this->getInformationElements();
        unset($informationElements['text.additionalInformation']);

        return $informationElements;
    }
}