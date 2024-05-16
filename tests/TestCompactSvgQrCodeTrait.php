<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill;

trait TestCompactSvgQrCodeTrait
{
    public function getCompact(): string
    {
        if (defined('Endroid\QrCode\Writer\SvgWriter::WRITER_OPTION_COMPACT')) {
            return '-compact';
        }

        return '';
    }
}
