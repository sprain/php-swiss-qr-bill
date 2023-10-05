<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\DataGroup;

/**
 * @internal
 */
interface QrCodeableInterface
{
    /**
     * @return list<string|int|null>
     */
    public function getQrCodeData(): array;
}
