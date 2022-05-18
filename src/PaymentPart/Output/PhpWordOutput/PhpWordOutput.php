<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput;

use PhpOffice\PhpWord\Element\Cell;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\LineSpacingRule;
use Sprain\SwissQrBill\Exception\InvalidPhpWordImageFormat;
use Sprain\SwissQrBill\PaymentPart\Output\AbstractOutput;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Amount;
use Sprain\SwissQrBill\PaymentPart\Output\Element\OutputElementInterface;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Placeholder;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Text;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Title;
use Sprain\SwissQrBill\PaymentPart\Output\OutputInterface;
use Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\Table\Bill;
use Sprain\SwissQrBill\PaymentPart\Translation\Translation;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;

final class PhpWordOutput extends AbstractOutput implements OutputInterface {

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

	const QR_CODE_SIZE = 4.6;

	private PhpWord $phpWord;
	private Bill $billTable;

	public function __construct(
			QrBill $qrBill,
			string $language,
			PhpWord $phpWord,
	) {
		parent::__construct($qrBill, $language);
		$this->phpWord = $phpWord;
		$this->defineFontStyles();
	}

	public function getPaymentPart() {
		$sections = $this->phpWord->getSections();
		$lastAddedSection = end($sections);
		$this->billTable = new Bill($lastAddedSection);

		$this->addInformationContentReceipt();
		$this->addCurrencyContentReceipt();
		$this->addAmountContentReceipt();

		$this->addSwissQrCodeImage();
		$this->addInformationContent();
		$this->addCurrencyContent();
		$this->addAmountContent();
		$this->addFurtherInformationContent();
	}

	public function setQrCodeImageFormat(string $fileExtension) : AbstractOutput {
		if ($fileExtension === QrCode::FILE_FORMAT_SVG) {
			throw new InvalidPhpWordImageFormat('SVG images are not allowed by PHPWord.');
		}

		$this->qrCodeImageFormat = $fileExtension;

		return $this;
	}

	private function addTitleElement(Cell $cell, Title $element, bool $isReceiptPart) : void {
		$text = Translation::get(str_replace("text.", "", $element->getTitle()), $this->language);
		$fStyle = $isReceiptPart ? self::FONT_STYLE_HEADING_RECEIPT : self::FONT_STYLE_HEADING_PAYMENT_PART;
		$cell->addText($text, $fStyle, $fStyle);
	}

	private function addTextElement(Cell $cell, Text $element, bool $isReceiptPart) : void {
		$fStyle = $isReceiptPart ? self::FONT_STYLE_VALUE_RECEIPT : self::FONT_STYLE_VALUE_PAYMENT_PART;
		$this->addElementTextRun($element->getText(), $cell->addTextRun($fStyle), $fStyle);
	}

	private function addAmountElement(Cell $cell, Amount $element, bool $isReceiptPart) : void {
		$fStyle = $isReceiptPart ? self::FONT_STYLE_AMOUNT_RECEIPT : self::FONT_STYLE_AMOUNT_PAYMENT_PART;
		$cell->addText($element->getText(), $fStyle, $fStyle);
	}

	private function addPlaceholderElement(Cell $cell, Placeholder $element, bool $isReceiptPart) : void {
		// TODO: implement image placeholders
	}

	private function addInformationContentReceipt() : void {
		$this->addReceiptTitle();

		$cell = $this->billTable->getReceipt()->getInformationSection();
		$informationElements = $this->getInformationElementsOfReceipt();
		$lastKey = array_key_last($informationElements);
		foreach ($informationElements as $key => $informationElement) {
			$this->addContentElement($cell, $informationElement, true);
			if($informationElement instanceof Text && $key !== $lastKey) {
				$cell->addText('', self::FONT_STYLE_VALUE_RECEIPT, self::FONT_STYLE_VALUE_RECEIPT);
			}
		}

		$this->addReceiptAcceptancePoint();
	}

	private function addContentElement(Cell $cell, OutputElementInterface $element, bool $isReceiptPart = false) : void {
		if ($element instanceof Title) {
			$this->addTitleElement($cell, $element, $isReceiptPart);
		}

		if ($element instanceof Text) {
			$this->addTextElement($cell, $element, $isReceiptPart);
		}

		if ($element instanceof Amount) {
			$this->addAmountElement($cell, $element, $isReceiptPart);
		}

		if ($element instanceof Placeholder) {
			$this->addPlaceholderElement($cell, $element, $isReceiptPart);
		}
	}

	private function addCurrencyContentReceipt() : void {
		$cell = $this->billTable->getReceipt()->getAmountSection()->getCurrencyCell();
		foreach ($this->getCurrencyElements() as $informationElement) {
			$this->addContentElement($cell, $informationElement, true);
		}
	}

	private function addAmountContentReceipt() : void {
		$cell = $this->billTable->getReceipt()->getAmountSection()->getAmountCell();
		foreach ($this->getAmountElementsReceipt() as $informationElement) {
			$this->addContentElement($cell, $informationElement, true);
		}
	}

	private function addReceiptTitle() : void {
		$text = Translation::get('receipt', $this->language);
		$this->billTable->getReceipt()->getTitleSection()->addText($text, self::FONT_STYLE_TITLE);
	}

	private function addReceiptAcceptancePoint() : void {
		$text = Translation::get('acceptancePoint', $this->language);
		$this->billTable->getReceipt()->getAcceptancePointSection()->addText($text, self::FONT_STYLE_ACCEPTANCE_POINT, self::FONT_STYLE_ACCEPTANCE_POINT);
	}

	private function addSwissQrCodeImage() : void {
		$qrCode = $this->getQrCode()->writeDataUri();
		$img = base64_decode(preg_replace('#^data:image/[^;]+;base64,#', '', $qrCode));
		$this->billTable->getPayment()->getQrCodeSection()->addImage($img, [
				'width' => Converter::cmToPoint(self::QR_CODE_SIZE),
				'height' => Converter::cmToPoint(self::QR_CODE_SIZE),
		]);
	}

	private function addInformationContent() : void {
		$text = Translation::get('paymentPart', $this->language);
		$this->billTable->getPayment()->getTitleSection()->addText($text, self::FONT_STYLE_TITLE);
		$cell = $this->billTable->getPayment()->getInformationSection();
		$informationElements = $this->getInformationElements();
		$lastKey = array_key_last($informationElements);
		foreach ($this->getInformationElements() as $key => $informationElement) {
			$this->addContentElement($cell, $informationElement);
			if($informationElement instanceof Text && $key !== $lastKey) {
				$cell->addText('', self::FONT_STYLE_VALUE_PAYMENT_PART, self::FONT_STYLE_VALUE_PAYMENT_PART);
			}
		}
	}

	private function addCurrencyContent() : void {
		$cell = $this->billTable->getPayment()->getAmountSection()->getCurrencyCell();
		foreach ($this->getCurrencyElements() as $informationElement) {
			$this->addContentElement($cell, $informationElement);
		}
	}

	private function addAmountContent() : void {
		$cell = $this->billTable->getPayment()->getAmountSection()->getAmountCell();
		foreach ($this->getAmountElements() as $informationElement) {
			$this->addContentElement($cell, $informationElement);
		}
	}

	private function addFurtherInformationContent() : void {
		$cell = $this->billTable->getPayment()->getFurtherInformationSection();
		foreach ($this->getFurtherInformationElements() as $informationElement) {
			$this->addContentElement($cell, $informationElement);
		}
	}

	private function addElementTextRun(string $text, TextRun $textRun, array|string $fStyle = []) : void {
		$lines = explode("\n", $text);
		foreach ($lines as $key => $line) {
			$textRun->addText($line, $fStyle, $fStyle);
			if (array_key_last($lines) !== $key) {
				$textRun->addTextBreak();
			}
		}
	}

	private function defineFontStyles() : void {
		$this->phpWord->addFontStyle(
				self::FONT_STYLE_TITLE,
				[
						'name' => self::FONT_FAMILY,
						'size' => 11,
						'bold' => true,
				]
		);
		$this->phpWord->addFontStyle(
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
		$this->phpWord->addFontStyle(
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
		$this->phpWord->addFontStyle(
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
		$this->phpWord->addFontStyle(
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
		$this->phpWord->addFontStyle(
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
		$this->phpWord->addFontStyle(
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
		$this->phpWord->addFontStyle(
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
		$this->phpWord->addFontStyle(
				self::FONT_STYLE_FURTHER_INFORMATION_PAYMENT_PART,
				[
						'name' => self::FONT_FAMILY,
						'size' => 7,
				],
				[
						'spacing' => Converter::pointToTwip(8),
						'spacingLineRule' => LineSpacingRule::EXACT,
						'spaceAfter' => 0,
				]
		);
	}

}
