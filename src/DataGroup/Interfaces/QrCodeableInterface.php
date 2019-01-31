<?php

namespace Sprain\SwissQrBill\DataGroup\Interfaces;

interface QrCodeableInterface
{
    public function getQrCodeData() : array;
}