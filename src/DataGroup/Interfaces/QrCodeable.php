<?php

namespace Sprain\SwissQrBill\DataGroup\Interfaces;

interface QrCodeable
{
    public function getQrCodeData() : array;
}