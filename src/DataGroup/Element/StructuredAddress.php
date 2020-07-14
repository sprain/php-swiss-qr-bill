<?php

namespace Sprain\SwissQrBill\DataGroup\Element;

use Sprain\SwissQrBill\DataGroup\AddressInterface;
use Sprain\SwissQrBill\DataGroup\QrCodeableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class StructuredAddress implements AddressInterface, SelfValidatableInterface, QrCodeableInterface
{
    use SelfValidatableTrait;

    const ADDRESS_TYPE = 'S';

    /**
     * Name or company
     *
     * @var string
     */
    private $name;

    /**
     * Street / P.O. box
     *
     * May not include building or house number.
     *
     * @var string
     */
    private $street;

    /**
     * Building number
     *
     * @var string
     */
    private $buildingNumber;

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

    /**
     * Country (ISO 3166-1 alpha-2)
     *
     * @var string
     */
    private $country;

    public static function createWithoutStreet(
        string $name,
        string $postalCode,
        string $city,
        string $country
    ): self {
        $structuredAddress = new self();
        $structuredAddress->name = $name;
        $structuredAddress->postalCode = $postalCode;
        $structuredAddress->city = $city;
        $structuredAddress->country = strtoupper($country);

        return $structuredAddress;
    }

    public static function createWithStreet(
        string $name,
        string $street,
        ?string $buildingNumber,
        string $postalCode,
        string $city,
        string $country
    ): self {
        $structuredAddress = new self();
        $structuredAddress->name = $name;
        $structuredAddress->street = $street;
        $structuredAddress->buildingNumber = $buildingNumber;
        $structuredAddress->postalCode = $postalCode;
        $structuredAddress->city = $city;
        $structuredAddress->country = strtoupper($country);

        return $structuredAddress;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getBuildingNumber(): ?string
    {
        return $this->buildingNumber;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getFullAddress(): string
    {
        $address = $this->getName();

        if ($this->getStreet()) {
            $address .= "\n" . $this->getStreet();

            if ($this->getBuildingNumber()) {
                $address .= " " . $this->getBuildingNumber();
            }
        }

        if (in_array($this->getCountry(), ['CH', 'FL'])) {
            $address .= sprintf("\n%s %s", $this->getPostalCode(), $this->getCity());
        } else {
            $address .= sprintf("\n%s-%s %s", $this->getCountry(), $this->getPostalCode(), $this->getCity());
        }

        return $address;
    }

    public function getQrCodeData(): array
    {
        return [
            $this->getCity() ? self::ADDRESS_TYPE : '',
            $this->getName(),
            $this->getStreet(),
            $this->getBuildingNumber(),
            $this->getPostalCode(),
            $this->getCity(),
            $this->getCountry()
        ];
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraints('name', [
            new Assert\NotBlank(),
            new Assert\Length([
                'max' => 70
            ])
        ]);

        $metadata->addPropertyConstraints('street', [
            new Assert\Length([
                'max' => 70
            ])
        ]);

        $metadata->addPropertyConstraints('buildingNumber', [
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

        $metadata->addPropertyConstraints('country', [
            new Assert\NotBlank(),
            new Assert\Country()
        ]);
    }
}
