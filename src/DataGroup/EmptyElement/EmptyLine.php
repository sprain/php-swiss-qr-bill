<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\DataGroup\EmptyElement;

use Sprain\SwissQrBill\DataGroup\QrCodeableInterface;

/**
 * @internal
 */
final class EmptyLine implements QrCodeableInterface
{
    #[\Override]
    public function getQrCodeData(): array
    {
        return [
            null
        ];
    }
}
