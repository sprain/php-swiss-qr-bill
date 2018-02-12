<?php

namespace Sprain\SwissQrBill\DataGroups;

use Sprain\SwissQrBill\DataGroups\Interfaces\QrCodeData;

class AlternativeScheme implements QrCodeData
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

    public function getQrCodeData() : array
    {
        return [
            $this->getParameter()
        ];
    }
}