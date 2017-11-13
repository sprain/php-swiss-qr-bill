<?php

namespace Sprain\SwissQrBill\DataGroups;

class AlternativeScheme
{
    private $parameter;


    public function getParameter(): string
    {
        return $this->parameter;
    }

    public function setParameter(string $parameter) : self
    {
        $this->parameter = $parameter;

        return $this;
    }
}