<?php

namespace Sprain\SwissQrBill\DataGroups\Interfaces;

interface PersonInterface
{
    public function getName(): string;

    public function setName(string $name);

    public function getStreet(): string;

    public function setStreet(string $street);

    public function getStreetNumber(): string;

    public function setStreetNumber(string $streetNumber);

    public function getPostalCode(): string;

    public function setPostalCode(string $postalCode);

    public function getCity(): string;

    public function setCity(string $city);

    public function getCountry(): string;

    public function setCountry(string $country);
}