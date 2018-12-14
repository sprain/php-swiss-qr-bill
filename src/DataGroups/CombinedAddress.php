<?php

namespace Sprain\SwissQrBill\DataGroups;

use Sprain\SwissQrBill\DataGroups\Abstracts\Address;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class CombinedAddress extends Address
{
    const ADDRESS_TYPE = 'K';

    /**
     * Address line 1
     *
     * Street and building number or P.O. Box
     *
     * @var string
     */
    private $addressLine1;

    /**
     * Address line 2
     *
     * Postal code and town
     *
     * @var string
     */
    private $addressLine2;

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function setAddressLine1(string $addressLine1 = null): self
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function setAddressLine2(string $addressLine2): self
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    public function getFullAddress() : string
    {
        $address = $this->getName();

        if ($this->getAddressLine1()) {
            $address .= "\n" . $this->getAddressLine1();
        }

        $address .= sprintf("\n%s-%s", $this->getCountry(), $this->getAddressLine2());

        return $address;
    }

    public function getQrCodeData() : array
    {
        return [
            $this->getName(),
            self::ADDRESS_TYPE,
            $this->getAddressLine1(),
            $this->getAddressLine2(),
            '',
            '',
            $this->getCountry()
        ];
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraints('addressLine1', [
            new Assert\Length([
                'max' => 70
            ])
        ]);

        $metadata->addPropertyConstraints('addressLine2', [
            new Assert\NotBlank(),
            new Assert\Length([
                'max' => 70
            ])
        ]);
    }
}