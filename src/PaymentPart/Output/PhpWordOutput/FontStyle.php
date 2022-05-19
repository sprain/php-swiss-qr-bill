<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\LineSpacingRule;

abstract class FontStyle {

	const FONT_FAMILY = 'Helvetica';
	const FONT_STYLE_TITLE = 'SwissBill Title';
	const FONT_STYLE_HEADING_RECEIPT = 'SwissBill Receipt Heading';
	const FONT_STYLE_VALUE_RECEIPT = 'SwissBill Receipt Value';
	const FONT_STYLE_AMOUNT_RECEIPT = 'SwissBill Receipt Amount';
	const FONT_STYLE_ACCEPTANCE_POINT = 'SwissBill Acceptance point';
	const FONT_STYLE_HEADING_PAYMENT_PART = 'SwissBill Payment part Heading';
	const FONT_STYLE_VALUE_PAYMENT_PART = 'SwissBill Payment part Value';
	const FONT_STYLE_AMOUNT_PAYMENT_PART = 'SwissBill Payment part Amount';
	const FONT_STYLE_FURTHER_INFORMATION_PAYMENT_PART = 'SwissBill Payment part Further information';
	const FONT_STYLE_SEPARATOR = 'SwissBill Separator';

	private static string $currentText = self::FONT_STYLE_VALUE_RECEIPT;

	public static function getCurrentText() : string {
		return self::$currentText;
	}

	public static function setCurrentText(string $fStyle) {
		self::$currentText = $fStyle;
	}

	public static function defineFontStyles(PhpWord $phpWord) : void {
		$phpWord->addFontStyle(
				self::FONT_STYLE_TITLE,
				[
						'name' => self::FONT_FAMILY,
						'size' => 11,
						'bold' => true,
				]
		);
		$phpWord->addFontStyle(
				self::FONT_STYLE_HEADING_RECEIPT,
				[
						'name' => self::FONT_FAMILY,
						'size' => 6,
						'bold' => true,
				],
				[
						'spacing' => Converter::pointToTwip(9),
						'spacingLineRule' => LineSpacingRule::EXACT,
						'spaceAfter' => 0,
				]
		);
		$phpWord->addFontStyle(
				self::FONT_STYLE_VALUE_RECEIPT,
				[
						'name' => self::FONT_FAMILY,
						'size' => 8,
				],
				[
						'spacing' => Converter::pointToTwip(9),
						'spacingLineRule' => LineSpacingRule::EXACT,
						'spaceAfter' => 0,
				]
		);
		$phpWord->addFontStyle(
				self::FONT_STYLE_AMOUNT_RECEIPT,
				[
						'name' => self::FONT_FAMILY,
						'size' => 8,
				],
				[
						'spacing' => Converter::pointToTwip(11),
						'spacingLineRule' => LineSpacingRule::EXACT,
						'spaceAfter' => 0,
				]
		);
		$phpWord->addFontStyle(
				self::FONT_STYLE_ACCEPTANCE_POINT,
				[
						'name' => self::FONT_FAMILY,
						'size' => 6,
						'bold' => true,
				],
				[
						'spacing' => Converter::pointToTwip(8),
						'spacingLineRule' => LineSpacingRule::EXACT,
						'spaceAfter' => 0,
						'alignment' => Jc::END,
				]
		);
		$phpWord->addFontStyle(
				self::FONT_STYLE_HEADING_PAYMENT_PART,
				[
						'name' => self::FONT_FAMILY,
						'size' => 8,
						'bold' => true,
				],
				[
						'spacing' => Converter::pointToTwip(11),
						'spacingLineRule' => LineSpacingRule::EXACT,
						'spaceAfter' => 0,
				]
		);
		$phpWord->addFontStyle(
				self::FONT_STYLE_VALUE_PAYMENT_PART,
				[
						'name' => self::FONT_FAMILY,
						'size' => 10,
				],
				[
						'spacing' => Converter::pointToTwip(11),
						'spacingLineRule' => LineSpacingRule::EXACT,
						'spaceAfter' => 0,
				]
		);
		$phpWord->addFontStyle(
				self::FONT_STYLE_AMOUNT_PAYMENT_PART,
				[
						'name' => self::FONT_FAMILY,
						'size' => 10,
				],
				[
						'spacing' => Converter::pointToTwip(13),
						'spacingLineRule' => LineSpacingRule::EXACT,
						'spaceAfter' => 0,
				]
		);

		$fontStyle = [
				'name' => self::FONT_FAMILY,
				'size' => 7,
		];
		$paragraphStyle = [
				'spacing' => Converter::pointToTwip(8),
				'spacingLineRule' => LineSpacingRule::EXACT,
				'spaceAfter' => 0,
		];
		$phpWord->addFontStyle(
				self::FONT_STYLE_FURTHER_INFORMATION_PAYMENT_PART,
				$fontStyle,
				$paragraphStyle
		);
		$phpWord->addFontStyle(
				self::FONT_STYLE_SEPARATOR,
				$fontStyle,
				array_merge($paragraphStyle, ['alignment' => Jc::CENTER]),
		);
	}

}
