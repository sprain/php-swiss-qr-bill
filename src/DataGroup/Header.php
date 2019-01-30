<?php

namespace Sprain\SwissQrBill\DataGroup;

use Sprain\SwissQrBill\DataGroup\Interfaces\QrCodeData;
use Sprain\SwissQrBill\Validator\Interfaces\Validatable;
use Sprain\SwissQrBill\Validator\ValidatorTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class Header implements QrCodeData, Validatable
{
    use ValidatorTrait;

    const QRTYPE_SPC = 'SPC';
    const VERSION_0200 = '0200';
    const CODING_LATIN = 1;

    /**
     * Unambiguous indicator for the Swiss QR code.
     *
     * @var string
     */
    private $qrType;

    /**
     * Version of the specifications (Implementation Guidelines) in use on
     * the date on which the Swiss QR code was created.
     * The first two positions indicate the main version, the following the
     * two positions the sub-version ("0200" for version 2.0).
     *
     * @var string
     */
    private $version;

    /**
     * Character set code
     *
     * @var int
     */
    private $coding;


    public function getQrType(): ?string
    {
        return $this->qrType;
    }

    public function setQrType(string $qrType) : self
    {
        $this->qrType = $qrType;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version) : self
    {
        $this->version = $version;

        return $this;
    }

    public function getCoding(): ?int
    {
        return $this->coding;
    }

    public function setCoding(int $coding) : self
    {
        $this->coding = $coding;

        return $this;
    }

    public function getQrCodeData() : array
    {
        return [
            $this->getQrType(),
            $this->getVersion(),
            $this->getCoding()
        ];
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