<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output;

use Sprain\SwissQrBill\QrBill;

interface OutputInterface
{
    public function getQrBill(): ?QrBill;

    public function getLanguage(): ?string;

    public function getPaymentPart(): ?string;

    public function setPrintable(bool $printable): static;

    public function isPrintable(): bool;

    public function setQrCodeImageFormat(string $imageFormat): static;

    public function getQrCodeImageFormat(): string;
}
