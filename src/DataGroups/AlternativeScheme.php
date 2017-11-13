<?php

namespace Sprain\SwissQrBill\DataGroups;

class AlternativeScheme
{
    /**
     * Parameter character chain of the alternative scheme
     * 
     * @var string
     */
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