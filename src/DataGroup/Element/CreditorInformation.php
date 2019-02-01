<?php

namespace Sprain\SwissQrBill\DataGroup\Element;

use Sprain\SwissQrBill\DataGroup\QrCodeableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadataInterface;

class CreditorInformation implements QrCodeableInterface, SelfValidatableInterface
{
    use SelfValidatableTrait;

    /**
     * IBAN or QR-IBAN of the creditor
     *
     * @var string
     */
    private $iban;

    public static function create(string $iban) : self
    {
        $creditorInformation = new self();
        $creditorInformation->iban = $iban;

        return $creditorInformation;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function getQrCodeData() : array
    {
        return [
            $this->getIban()
        ];
    }

    public static function loadValidatorMetadata(ClassMetadataInterface $metadata) : void
    {
        // Only IBANs with CH or LI country code
        $metadata->addPropertyConstraints('iban', [
            new Assert\NotBlank(),
            new Assert\Iban(),
            new Assert\Regex([
                'pattern' => '/^(CH|LI)/',
                'match' => true
            ])
        ]);
    }
}