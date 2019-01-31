<?php

namespace Sprain\SwissQrBill\DataGroup;

interface QrCodeableInterface
{
    public function getQrCodeData() : array;
}