<?php

namespace Sprain\SwissQrBill\Traits;

trait PersonTrait
{
    private $name;

    private $street;

    private $streetNumber;

    private $postalCode;

    private $city;

    private $country;


    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name) : self
    {
        $this->name = $name;

        return $this;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street) : self
    {
        $this->street = $street;

        return $this;
    }

    public function getStreetNumber(): string
    {
        return $this->streetNumber;
    }

    public function setStreetNumber(string $streetNumber) : self
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode) : self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city) : self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country) : self
    {
        $this->country = $country;

        return $this;
    }
}