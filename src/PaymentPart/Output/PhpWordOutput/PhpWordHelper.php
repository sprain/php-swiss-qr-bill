<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput;

use PhpOffice\PhpWord\Shared\Converter;

abstract class PhpWordHelper
{
    public static function mmToTwip(int|float $mm) : float|int
    {
        return Converter::cmToTwip($mm / 10);
    }

    public static function mmToPoint(int|float $mm) : float|int
    {
        return Converter::cmToPoint($mm / 10);
    }

    /**
     * Convert percentage to fiftieths (1/50) of a percent (1% = 50 unit).
     *
     * @param int|float $percent
     * @return int|float
     */
    public static function percentToPct(int|float $percent) : int|float
    {
        return $percent * 50;
    }
}
