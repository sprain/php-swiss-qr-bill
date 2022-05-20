<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput;

use PhpOffice\PhpWord\Element\Cell;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Image;
use Sprain\SwissQrBill\Exception\InvalidPhpWordImageFormat;
use Sprain\SwissQrBill\PaymentPart\Output\AbstractOutput;
use Sprain\SwissQrBill\PaymentPart\Output\Element\OutputElementInterface;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Placeholder;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Text;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Title;
use Sprain\SwissQrBill\PaymentPart\Output\OutputInterface;
use Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\Table\Bill;
use Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\Table\Style;
use Sprain\SwissQrBill\PaymentPart\Translation\Translation;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;

final class PhpWordOutput extends AbstractOutput implements OutputInterface
{
    private PhpWord $phpWord;
    private Bill $billTable;

    public function __construct(
        QrBill $qrBill,
        string $language,
        PhpWord $phpWord,
    ) {
        parent::__construct($qrBill, $language);
        $this->phpWord = $phpWord;
        FontStyle::defineFontStyles($this->phpWord);
    }

    public function getPaymentPart()
    {
        $sections = $this->phpWord->getSections();
        $lastAddedSection = end($sections);
        $this->billTable = new Bill($lastAddedSection, $this->isPrintable());

        $this->addSeparatorContentIfNotPrintable();

        $this->addInformationContentReceipt();
        $this->addCurrencyContentReceipt();
        $this->addAmountContentReceipt();

        $this->addSwissQrCodeImage();
        $this->addInformationContent();
        $this->addCurrencyContent();
        $this->addAmountContent();
        $this->addFurtherInformationContent();
    }

    public function setQrCodeImageFormat(string $fileExtension) : AbstractOutput
    {
        if ($fileExtension === QrCode::FILE_FORMAT_SVG) {
            throw new InvalidPhpWordImageFormat('SVG images are not allowed by PHPWord.');
        }

        $this->qrCodeImageFormat = $fileExtension;

        return $this;
    }

    private function addTitleElement(Cell $cell, Title $element, bool $isReceiptPart) : void
    {
        $text = Translation::get(str_replace("text.", "", $element->getTitle()), $this->language);
        $fStyle = $isReceiptPart ? FontStyle::FONT_STYLE_HEADING_RECEIPT : FontStyle::FONT_STYLE_HEADING_PAYMENT_PART;
        $cell->addText($text, $fStyle, $fStyle);
    }

    private function addTextElement(Cell $cell, Text $element) : void
    {
        $fStyle = FontStyle::getCurrentText();
        $this->addElementTextRun($element->getText(), $cell->addTextRun($fStyle), $fStyle);
    }

    private function addPlaceholderElement(Cell $cell, Placeholder $element, bool $isReceiptPart) : void
    {
        $type = $element->getType();

        switch ($type) {
            case Placeholder::PLACEHOLDER_TYPE_AMOUNT['type']:
                $cell->addImage($element->getFile(Placeholder::FILE_TYPE_PNG), [
                        'width' => PhpWordHelper::mmToPoint($element->getWidth()),
                        'height' => PhpWordHelper::mmToPoint($element->getHeight()),
                        'positioning' => Image::POSITION_ABSOLUTE,
                        'posHorizontal' => Image::POSITION_ABSOLUTE,
                        'posVertical' => Image::POSITION_ABSOLUTE,
                        'marginLeft' => PhpWordHelper::mmToPoint(-4.5),
                        'marginTop' => PhpWordHelper::mmToPoint(0.8),
                ]);
                break;
            case Placeholder::PLACEHOLDER_TYPE_AMOUNT_RECEIPT['type']:
                $cell->addImage($element->getFile(Placeholder::FILE_TYPE_PNG), [
                        'width' => PhpWordHelper::mmToPoint($element->getWidth()),
                        'height' => PhpWordHelper::mmToPoint($element->getHeight()),
                        'positioning' => Image::POSITION_ABSOLUTE,
                        'posHorizontal' => Image::POSITION_ABSOLUTE,
                        'posVertical' => Image::POSITION_ABSOLUTE,
                        'marginLeft' => PhpWordHelper::mmToPoint(9.1),
                        'marginTop' => PhpWordHelper::mmToPoint(-2.5),
                ]);
                break;
            case Placeholder::PLACEHOLDER_TYPE_PAYABLE_BY['type']:
            case Placeholder::PLACEHOLDER_TYPE_PAYABLE_BY_RECEIPT['type']:
            default:
                $cell->addImage($element->getFile(Placeholder::FILE_TYPE_PNG), [
                        'width' => PhpWordHelper::mmToPoint($element->getWidth()),
                        'height' => PhpWordHelper::mmToPoint($element->getHeight()),
                        'wrappingStyle' => Image::WRAP_INFRONT,
                        'positioning' => Image::POS_RELATIVE,
                        'posHorizontalRel' => Image::POS_RELTO_MARGIN,
                        'posVerticalRel' => Image::POS_RELTO_LINE,
                ]);
        }
    }

    private function addSeparatorContentIfNotPrintable() : void
    {
        if (!$this->isPrintable()) {
            $text = Translation::get('separate', $this->language);
            $fStyle = FontStyle::FONT_STYLE_SEPARATOR;
            $this->billTable->getSeparate()->addText($text, $fStyle, $fStyle);
        }
    }

    private function addInformationContentReceipt() : void
    {
        $this->addReceiptTitle();

        $cell = $this->billTable->getReceipt()->getInformationSection();
        $informationElements = $this->getInformationElementsOfReceipt();
        $lastKey = array_key_last($informationElements);
        FontStyle::setCurrentText(FontStyle::FONT_STYLE_VALUE_RECEIPT);
        foreach ($informationElements as $key => $informationElement) {
            $this->addContentElement($cell, $informationElement, true);
            if ($informationElement instanceof Text && $key !== $lastKey) {
                $cell->addText('', FontStyle::FONT_STYLE_VALUE_RECEIPT, FontStyle::FONT_STYLE_VALUE_RECEIPT);
            }
        }

        $this->addReceiptAcceptancePoint();
    }

    private function addContentElement(Cell $cell, OutputElementInterface $element, bool $isReceiptPart = false) : void
    {
        if ($element instanceof Title) {
            $this->addTitleElement($cell, $element, $isReceiptPart);
        }

        if ($element instanceof Text) {
            $this->addTextElement($cell, $element);
        }

        if ($element instanceof Placeholder) {
            $this->addPlaceholderElement($cell, $element, $isReceiptPart);
        }
    }

    private function addCurrencyContentReceipt() : void
    {
        $cell = $this->billTable->getReceipt()->getAmountSection()->getCurrencyCell();
        FontStyle::setCurrentText(FontStyle::FONT_STYLE_AMOUNT_RECEIPT);
        foreach ($this->getCurrencyElements() as $informationElement) {
            $this->addContentElement($cell, $informationElement, true);
        }
    }

    private function addAmountContentReceipt() : void
    {
        $cell = $this->billTable->getReceipt()->getAmountSection()->getAmountCell();
        FontStyle::setCurrentText(FontStyle::FONT_STYLE_AMOUNT_RECEIPT);
        foreach ($this->getAmountElementsReceipt() as $informationElement) {
            $this->addContentElement($cell, $informationElement, true);
        }
    }

    private function addReceiptTitle() : void
    {
        $text = Translation::get('receipt', $this->language);
        $this->billTable->getReceipt()->getTitleSection()->addText($text, FontStyle::FONT_STYLE_TITLE);
    }

    private function addReceiptAcceptancePoint() : void
    {
        $text = Translation::get('acceptancePoint', $this->language);
        $this->billTable->getReceipt()->getAcceptancePointSection()->addText(
            $text,
            FontStyle::FONT_STYLE_ACCEPTANCE_POINT,
            FontStyle::FONT_STYLE_ACCEPTANCE_POINT
        );
    }

    private function addSwissQrCodeImage() : void
    {
        $qrCode = $this->getQrCode()->writeDataUri();
        $img = base64_decode(preg_replace('#^data:image/[^;]+;base64,#', '', $qrCode));
        $this->billTable->getPayment()->getQrCodeSection()->addImage($img, [
                'width' => PhpWordHelper::mmToPoint(Style::QR_CODE_SIZE),
                'height' => PhpWordHelper::mmToPoint(Style::QR_CODE_SIZE),
        ]);
    }

    private function addInformationContent() : void
    {
        $text = Translation::get('paymentPart', $this->language);
        $this->billTable->getPayment()->getTitleSection()->addText($text, FontStyle::FONT_STYLE_TITLE);
        $cell = $this->billTable->getPayment()->getInformationSection();
        $informationElements = $this->getInformationElements();
        $lastKey = array_key_last($informationElements);
        FontStyle::setCurrentText(FontStyle::FONT_STYLE_VALUE_PAYMENT_PART);
        foreach ($this->getInformationElements() as $key => $informationElement) {
            $this->addContentElement($cell, $informationElement);
            if ($informationElement instanceof Text && $key !== $lastKey) {
                $cell->addText('', FontStyle::FONT_STYLE_VALUE_PAYMENT_PART, FontStyle::FONT_STYLE_VALUE_PAYMENT_PART);
            }
        }
    }

    private function addCurrencyContent() : void
    {
        $cell = $this->billTable->getPayment()->getAmountSection()->getCurrencyCell();
        FontStyle::setCurrentText(FontStyle::FONT_STYLE_AMOUNT_PAYMENT_PART);
        foreach ($this->getCurrencyElements() as $informationElement) {
            $this->addContentElement($cell, $informationElement);
        }
    }

    private function addAmountContent() : void
    {
        $cell = $this->billTable->getPayment()->getAmountSection()->getAmountCell();
        FontStyle::setCurrentText(FontStyle::FONT_STYLE_AMOUNT_PAYMENT_PART);
        foreach ($this->getAmountElements() as $informationElement) {
            $this->addContentElement($cell, $informationElement);
        }
    }

    private function addFurtherInformationContent() : void
    {
        $cell = $this->billTable->getPayment()->getFurtherInformationSection();
        FontStyle::setCurrentText(FontStyle::FONT_STYLE_FURTHER_INFORMATION_PAYMENT_PART);
        foreach ($this->getFurtherInformationElements() as $informationElement) {
            $this->addContentElement($cell, $informationElement);
        }
    }

    private function addElementTextRun(string $text, TextRun $textRun, array|string $fStyle = []) : void
    {
        $lines = explode("\n", $text);
        foreach ($lines as $key => $line) {
            $textRun->addText($line, $fStyle, $fStyle);
            if (array_key_last($lines) !== $key) {
                $textRun->addTextBreak();
            }
        }
    }
}
