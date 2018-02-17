<?php

namespace Sprain\SwissQrBill\DataGroups;

use Sprain\SwissQrBill\DataGroups\Interfaces\QrCodeData;
use Sprain\SwissQrBill\Validator\Interfaces\Validatable;
use Sprain\SwissQrBill\Validator\ValidatorTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class CreditorInformation implements QrCodeData, Validatable
{
    use ValidatorTrait;

    /**
     * IBAN or QR-IBAN of the creditor
     *
     * @var string
     */
    private $iban;

    public function getIban(): string
    {
        return $this->iban;
    }

    public function setIban(string $iban) : self
    {
        $this->iban = preg_replace('/\s/', '', $iban);

        return $this;
    }

    public function getQrCodeData() : array
    {
        return [
            $this->getIban()
        ];
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
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