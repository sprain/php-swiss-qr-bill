<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output;

use Sprain\SwissQrBill\QrBill;

interface OutputInterface
{
    public function getQrBill(): ?QrBill;

    public function getLanguage(): ?string;

    public function getPaymentPart(): ?string;

    public function setPrintOptions(PrintOptions $printOptions): static;

    public function getPrintOptions(): PrintOptions;

    public function setQrCodeImageFormat(string $imageFormat): static;

    public function getQrCodeImageFormat(): string;
}
