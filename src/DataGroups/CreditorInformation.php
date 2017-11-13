<?php

namespace Sprain\SwissQrBill\DataGroups;

class CreditorInformation
{
    private $iban;

    public function getIban(): string
    {
        return $this->iban;
    }

    public function setIban(string $iban) : self
    {
        $this->iban = $iban;

        return $this;
    }
}