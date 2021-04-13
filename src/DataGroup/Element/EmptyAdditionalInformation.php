<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\DataGroup\Element;

use Sprain\SwissQrBill\DataGroup\QrCodeableInterface;

final class EmptyAdditionalInformation implements QrCodeableInterface
{
    public const TRAILER_EPD = 'EPD';

    public function getQrCodeData(): array
    {
        return [
            null,
            self::TRAILER_EPD,
            null
        ];
    }
}
