<?php

namespace Sprain\SwissQrBill\DataGroups;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class Header
{
    /**
     * Unambiguous indicator for the Swiss QR code.
     * Fixed value "SPC" (Swiss Payments Code).
     */
    const QRTYPE_SPC = 'SPC';

    /**
     * Version of the specifications (Implementation Guidelines) in use on
     * the date on which the Swiss QR code was created.
     * The first two positions indicate the main version, the following the
     * two positions the sub-version ("0100" for version 1.0).
     */
    const VERSION_0100 = '0100';

    /**
     * Character set code.
     * Fixed value 1 indicates Latin character set.
     */
    const CODING_LATIN = 1;

    private $qrType;

    private $version;

    private $coding;

        public function getQrType(): string
    {
        return $this->qrType;
    }

    public function setQrType(string $qrType) : self
    {
        $this->qrType = $qrType;

        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version) : self
    {
        $this->version = $version;

        return $this;
    }

    public function getCoding(): int
    {
        return $this->coding;
    }

    public function setCoding(int $coding) : self
    {
        $this->coding = $coding;

        return $this;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // Fixed length, three-digit, alphanumeric
        $metadata->addPropertyConstraints('qrType', [
            new Assert\NotBlank(),
            new Assert\Regex([
                'pattern' => '/^[a-zA-Z0-9]{3}$/',
                'match' => true
            ])
        ]);

        // Fixed length, four-digit, numeric
        $metadata->addPropertyConstraints('version', [
            new Assert\NotBlank(),
            new Assert\Regex([
                'pattern' => '/^\d{4}$/',
                'match' => true
            ])
        ]);

        // One-digit, numeric
        $metadata->addPropertyConstraints('coding', [
            new Assert\NotBlank(),
            new Assert\Regex([
                'pattern' => '/^\d{1}$/',
                'match' => true
            ])
        ]);
    }
}