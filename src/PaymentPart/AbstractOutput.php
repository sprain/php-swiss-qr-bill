<?php

namespace Sprain\SwissQrBill\PaymentPart;

use Sprain\SwissQrBill\QrBill;

abstract class AbstractOutput
{
    /** @var QrBill */
    protected $qrBill;

    /** @var  string */
    protected $language;

    public function getQrBill() : ?QrBill
    {
        return $this->qrBill;
    }

    public function setQrBill(QrBill $qrBill) : self
    {
        $this->qrBill = $qrBill;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getInformationElements() : array
    {
        $informationElements = [];

        $availableInformationElements =  [
            'text.account' => $this->qrBill->getCreditorInformation()->getIban(),
            'text.creditor' => $this->qrBill->getCreditor() ? nl2br($this->qrBill->getCreditor()->getFullAddress()) : null,
            'text.referenceNumber' => $this->qrBill->getPaymentReference()->getReference(),
            'text.additionalInformation' => $this->qrBill->getAdditionalInformation()->getMessage(),
            'text.debtor' => $this->qrBill->getUltimateDebtor() ? nl2br($this->qrBill->getUltimateDebtor()->getFullAddress()) : null,
        ];

        foreach($availableInformationElements as $key => $content) {
            if ($content) {
                $informationElements[$key] = $content;
            }
        }

        return $informationElements;
    }
}