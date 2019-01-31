<?php

namespace Sprain\SwissQrBill\DataGroup;

use Sprain\SwissQrBill\DataGroup\QrCodeableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadataInterface;

class AdditionalInformation implements QrCodeableInterface, SelfValidatableInterface
{
    use SelfValidatableTrait;

    const TRAILER_EPD = 'EPD';

    /**
     * Unstructured information can be used to indicate the payment purpose
     * or for additional textual information about payments with a structured reference.
     * 
     * @var string
     */
    private $message;

    /**
     * Bill information contain coded information for automated booking of the payment.
     * The data is not forwarded with the payment.
     *
     * @var string
     */
    private $billInformation;

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message = null): self
    {
        $this->message = $message;

        return $this;
    }

    public function getBillInformation(): ?string
    {
        return $this->billInformation;
    }

    public function setBillInformation(string $billInformation = null) : self
    {
        $this->billInformation = $billInformation;

        return $this;
    }

    public function getQrCodeData() : array
    {
        return [
            $this->getMessage(),
            self::TRAILER_EPD,
            $this->getBillInformation()
        ];
    }

    public static function loadValidatorMetadata(ClassMetadataInterface $metadata) : void
    {
        $metadata->addPropertyConstraints('message', [
            new Assert\Length([
                'max' => 140
            ])
        ]);

        $metadata->addPropertyConstraints('billInformation', [
            new Assert\Length([
                'max' => 140
            ])
        ]);
    }
}