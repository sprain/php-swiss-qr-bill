<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput;

use PhpOffice\PhpWord\Shared\Converter;

abstract class PhpWordHelper {

	public static function mmToTwip(float $mm) : float|int
	{
			return Converter::cmToTwip($mm / 10);
	}

	public static function mmToPoint(float $mm) : float|int
	{
			return Converter::cmToPoint($mm / 10);
	}

}
