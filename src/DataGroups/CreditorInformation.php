<?php

namespace Sprain\SwissQrBill\DataGroups;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class CreditorInformation
{
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

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // Fixed length, 21 alphanumeric characters, only IBANs with CH or LI country code
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