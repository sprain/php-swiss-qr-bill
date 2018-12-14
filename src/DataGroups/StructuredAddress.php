<?php

namespace Sprain\SwissQrBill\DataGroups;

use Sprain\SwissQrBill\DataGroups\Abstracts\Address;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class StructuredAddress extends Address
{
    const ADDRESS_TYPE = 'S';

    /**
     * Street / P.O. box of the creditor
     *
     * May not include building or house number.
     *
     * @var string
     */
    private $street;

    /**
     * House number of the creditor
     *
     * @var string
     */
    private $houseNumber;

    /**
     * Postal code without county code
     *
     * @var string
     */
    private $postalCode;

    /**
     * City
     *
     * @var string
     */
    private $city;

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street = null) : self
    {
        $this->street = $street;

        return $this;
    }

    public function getHouseNumber(): ?string
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(string $houseNumber = null) : self
    {
        $this->houseNumber = $houseNumber;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode) : self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city) : self
    {
        $this->city = $city;

        return $this;
    }

    public function getFullAddress() : string
    {
        $address = $this->getName();

        if ($this->getStreet()) {
            $address .= "\n" . $this->getStreet();

            if ($this->getHouseNumber()) {
                $address .= " " . $this->getHouseNumber();
            }
        }

        $address .= sprintf("\n%s-%s %s", $this->getCountry(), $this->getPostalCode(), $this->getCity());

        return $address;
    }

    public function getQrCodeData() : array
    {
        return [
            self::ADDRESS_TYPE,
            $this->getName(),
            $this->getStreet(),
            $this->getHouseNumber(),
            $this->getPostalCode(),
            $this->getCity(),
            $this->getCountry()
        ];
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraints('street', [
            new Assert\Length([
                'max' => 70
            ])
        ]);

        $metadata->addPropertyConstraints('houseNumber', [
            new Assert\Length([
                'max' => 16
            ])
        ]);

        $metadata->addPropertyConstraints('postalCode', [
            new Assert\NotBlank(),
            new Assert\Length([
                'max' => 16
            ])
        ]);

        $metadata->addPropertyConstraints('city', [
            new Assert\NotBlank(),
            new Assert\Length([
                'max' => 35
            ])
        ]);
    }
}