<?php

namespace Sprain\SwissQrBill\DataGroups;

class Header
{
    /**
     * Unambiguous indicator for the Swiss QR code.
     * Fixed value "SPC" (Swiss Payments Code).
     */
    const QRTYPE_SPC = 'SPC';

    /**
     * Version of the specifications (Implementation Guidelines) in use on
     * the date on which the Swiss QR code was created.
     * The first two positions indicate the main version, the following the
     * two positions the sub-version ("0100" for version 1.0).
     */
    const VERSION_0100 = '0100';

    /**
     * Character set code. Fixed value 1 indicates Latin character set.
     */
    const CODING_LATIN = '1';

    private $qrType;

    private $version;

    private $coding;


    public function getQrType(): string
    {
        return $this->qrType;
    }

    public function setQrType(string $qrType) : self
    {
        $this->qrType = $qrType;

        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version) : self
    {
        $this->version = $version;

        return $this;
    }

    public function getCoding(): string
    {
        return $this->coding;
    }

    public function setCoding(string $coding) : self
    {
        $this->coding = $coding;

        return $this;
    }
}