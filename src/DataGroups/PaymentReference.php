<?php

namespace Sprain\SwissQrBill\DataGroups;

class PaymentReference
{
    const TYPE_QR = 'QRR';
    const TYPE_SCOR = 'SCOR';
    const TYPE_NON = 'NON';

    /**
     * Reference type
     *
     * @var string
     */
    private $type;

    /**
     * Strutured reference number
     * Either a QR reference or a Creditor Reference (ISO 11649)
     *
     * @var string
     */
    private $reference;

    /**
     * Unstructured message
     *
     * @var string
     */
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