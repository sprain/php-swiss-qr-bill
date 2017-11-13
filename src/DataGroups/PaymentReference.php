<?php

namespace Sprain\SwissQrBill\DataGroups;

class PaymentReference
{
    const TYPE_QR = 'QRR';
    const TYPE_SCOR = 'SCOR';
    const TYPE_NON = 'NON';

    private $type;

    private $reference;

    private $message;


    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type) : self
    {
        $this->type = $type;

        return $this;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference) : self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message) : self
    {
        $this->message = $message;

        return $this;
    }
}