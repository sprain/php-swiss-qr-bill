<?php

namespace Sprain\SwissQrBill\PaymentPart;

use Sprain\SwissQrBill\QrBill;

interface OutputInterface
{
    public function getQrBill() : ?QrBill;

    public function getLanguage(): ?string;

    public function getPaymentPart();
}