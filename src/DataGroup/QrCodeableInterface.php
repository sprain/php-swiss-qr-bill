<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\DataGroup;

interface QrCodeableInterface
{
    public function getQrCodeData(): array;
}
