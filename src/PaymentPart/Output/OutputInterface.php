<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output;

use Sprain\SwissQrBill\QrBill;

interface OutputInterface
{
    public function getQrBill(): ?QrBill;

    public function getLanguage(): ?string;

    public function getPaymentPart(): ?string;

    public function setDisplayOptions(DisplayOptions $displayOptions): static;

    public function getDisplayOptions(): DisplayOptions;

    public function setQrCodeImageFormat(string $imageFormat): static;

    public function getQrCodeImageFormat(): string;
}
