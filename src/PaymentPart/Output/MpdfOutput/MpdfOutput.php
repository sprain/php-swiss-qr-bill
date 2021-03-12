<?php declare(strict_types=1);


namespace Sprain\SwissQrBill\PaymentPart\Output\MpdfOutput;

use Mpdf\Mpdf;
use Sprain\SwissQrBill\Exception\InvalidMpdfImageFormat;
use Sprain\SwissQrBill\PaymentPart\Output\AbstractOutput;
use Sprain\SwissQrBill\PaymentPart\Output\Element\OutputElementInterface;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Placeholder;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Text;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Title;
use Sprain\SwissQrBill\PaymentPart\Output\OutputInterface;
use Sprain\SwissQrBill\PaymentPart\Translation\Translation;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;

final class MpdfOutput extends AbstractOutput implements OutputInterface
{
    // MPDF constants
    private const BORDER = 0;
    private const ALIGN_LEFT = 'L';
    private const ALIGN_RIGHT = 'R';
    private const ALIGN_CENTER = 'C';

    // Location constants
    private const CURRENCY_AMOUNT_Y = 259.3;
    private const AMOUNT_LINE_SPACING = 1.2;
    private const AMOUNT_LINE_SPACING_RCPT = 0.6;
    private const LEFT_PART_X = 4;
    private const RIGHT_PART_X = 66;
    private const RIGHT_PART_X_INFO = 117;
    private const TITLE_Y = 195.2;

    // Font constants
    private const FONT_SIZE_MAIN_TITLE = 11;
    private const FONT_SIZE_TITLE_RECEIPT = 6;
    private const FONT_SIZE_RECEIPT = 8;
    private const FONT_SIZE_TITLE_PAYMENT_PART = 8;
    private const FONT_SIZE_PAYMENT_PART = 10;
    private const FONT_SIZE_FURTHER_INFORMATION = 7;

    // Line spacing constants
    private const LINE_SPACING_RECEIPT = 3.4;
    private const LINE_SPACING_PAYMENT_PART = 4.8;

    /** @var Mpdf */
    private $mpdf;

    /** @var float */
    private $amountLS = 0;

    /* @var float */
    private $offsetX;

    /* @var float */
    private $offsetY;

    public function __construct(
        QrBill $qrBill,
        string $language,
        Mpdf $mpdf,
        float $offsetX = 0,
        float $offsetY = 0,
        string $font = 'Helvetica'
    ) {
        parent::__construct($qrBill, $language);
        $this->mpdf = $mpdf;
        $this->offsetX = $offsetX;
        $this->offsetY = $offsetY;
        $this->font = $font;
        $this->setQrCodeImageFormat(QrCode::FILE_FORMAT_PNG);
    }

    public function getPaymentPart()
    {
        $this->mpdf->SetAutoPageBreak(false);

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

    public function setQrCodeImageFormat(string $fileExtension): AbstractOutput
    {
        if ($fileExtension === 'svg') {
            throw new InvalidMpdfImageFormat('SVG images are not allowed by MPDF.');
        }

        $this->qrCodeImageFormat = $fileExtension;

        return $this;
    }

    private function addSwissQrCodeImage(): void
    {
        $qrCode = $this->getQrCode();
        $qrCode->setWriterByExtension(
            $this->getQrCodeImageFormat()
        );

        $yPosQrCode = 209.5 + $this->offsetY;
        $xPosQrCode = 67 + $this->offsetX;

        $this->mpdf->Image($qrCode->writeDataUri(), $xPosQrCode, $yPosQrCode, 46, 46, 'png');
    }

    private function addInformationContentReceipt(): void
    {
        // Title
        $this->mpdf->SetFont($this->font, 'B', self::FONT_SIZE_MAIN_TITLE);
        $this->SetXY(self::LEFT_PART_X, self::TITLE_Y);
        $this->mpdf->MultiCell(0, 7, Translation::get('receipt', $this->language));

        // Elements
        $this->SetY(204);
        foreach ($this->getInformationElementsOfReceipt() as $receiptInformationElement) {
            $this->SetX(self::LEFT_PART_X);
            $this->setContentElement($receiptInformationElement, true);
        }

        // Acceptance section
        $this->mpdf->SetFont($this->font, 'B', self::FONT_SIZE_TITLE_RECEIPT);
        $this->SetXY(self::LEFT_PART_X, 274.3);
        $this->mpdf->Cell(54, 0, Translation::get('acceptancePoint', $this->language), self::BORDER, '', self::ALIGN_RIGHT);
    }

    private function addInformationContent(): void
    {
        // Title
        $this->mpdf->SetFont($this->font, 'B', self::FONT_SIZE_MAIN_TITLE);
        $this->SetXY(self::RIGHT_PART_X, 195.2);
        $this->mpdf->MultiCell(48, 7, Translation::get('paymentPart', $this->language));

        // Elements
        $this->SetY(197.3);
        foreach ($this->getInformationElements() as $informationElement) {
            $this->SetX(self::RIGHT_PART_X_INFO);
            $this->setContentElement($informationElement, false);
        }
    }

    private function addCurrencyContentReceipt(): void
    {
        $this->SetY(self::CURRENCY_AMOUNT_Y);
        foreach ($this->getCurrencyElements() as $receiptCurrencyElement) {
            $this->amountLS = self::AMOUNT_LINE_SPACING_RCPT;
            $this->SetX(self::LEFT_PART_X);
            $this->setContentElement($receiptCurrencyElement, true);
            $this->amountLS = 0;
        }
    }

    private function addAmountContentReceipt(): void
    {
        $this->SetY(self::CURRENCY_AMOUNT_Y);
        foreach ($this->getAmountElementsReceipt() as $receiptAmountElement) {
            $this->amountLS = self::AMOUNT_LINE_SPACING_RCPT;
            $this->SetX(16);
            $this->setContentElement($receiptAmountElement, true);
            $this->amountLS = 0;
        }
    }

    private function addCurrencyContent(): void
    {
        $this->SetY(self::CURRENCY_AMOUNT_Y);
        foreach ($this->getCurrencyElements() as $currencyElement) {
            $this->amountLS = self::AMOUNT_LINE_SPACING;
            $this->SetX(self::RIGHT_PART_X);
            $this->setContentElement($currencyElement, false);
            $this->amountLS = 0;
        }
    }

    private function addAmountContent(): void
    {
        $this->SetY(self::CURRENCY_AMOUNT_Y);
        foreach ($this->getAmountElements() as $amountElement) {
            $this->amountLS = self::AMOUNT_LINE_SPACING;
            $this->SetX(80);
            $this->setContentElement($amountElement, false);
            $this->amountLS = 0;
        }
    }

    private function addFurtherInformationContent(): void
    {
        $this->SetXY(self::RIGHT_PART_X, 286);
        $this->mpdf->SetFont($this->font, '', self::FONT_SIZE_FURTHER_INFORMATION);

        foreach ($this->getFurtherInformationElements() as $furtherInformationElement) {
            $this->SetX(self::RIGHT_PART_X);
            $this->setContentElement($furtherInformationElement, true);
        }
    }

    private function addSeparatorContentIfNotPrintable()
    {
        if (!$this->isPrintable()) {
            $this->mpdf->SetLineWidth(0.1);
            $this->mpdf->Line(2 + $this->offsetX, 193 + $this->offsetY, 208 + $this->offsetX, 193 + $this->offsetY);
            $this->mpdf->Line(62 + $this->offsetX, 193 + $this->offsetY, 62 + $this->offsetX, 296 + $this->offsetY);
            $this->mpdf->SetFont($this->font, '', self::FONT_SIZE_FURTHER_INFORMATION);
            $this->SetY(189.6);
            $this->mpdf->MultiCell(0, 0, Translation::get('separate', $this->language), self::BORDER, self::ALIGN_CENTER);
        }
    }

    private function setContentElement(OutputElementInterface $element, bool $isReceiptPart): void
    {
        if ($element instanceof Title) {
            $this->setTitleElement($element, $isReceiptPart);
        }

        if ($element instanceof Text) {
            $this->setTextElement($element, $isReceiptPart);
        }

        if ($element instanceof Placeholder) {
            $this->setPlaceholderElement($element);
        }
    }

    private function setTitleElement(Title $element, bool $isReceiptPart): void
    {
        $this->mpdf->SetFont($this->font, 'B', $isReceiptPart ? self::FONT_SIZE_TITLE_RECEIPT : self::FONT_SIZE_TITLE_PAYMENT_PART);
        $this->mpdf->MultiCell(0, 2.8,
            Translation::get(str_replace("text.", "", $element->getTitle()), $this->language)
        );
        $this->mpdf->Ln($this->amountLS);
    }

    private function setTextElement(Text $element, bool $isReceiptPart): void
    {
        $this->mpdf->SetFont($this->font, '', $isReceiptPart ? self::FONT_SIZE_RECEIPT : self::FONT_SIZE_PAYMENT_PART);
        $this->mpdf->MultiCell(
            $isReceiptPart ? 54 : 0,
            $isReceiptPart ? 3.3 : 4,
            str_replace("text.", "", $element->getText()),
            self::BORDER,
            self::ALIGN_LEFT
        );
        $this->mpdf->Ln($isReceiptPart ? self::LINE_SPACING_RECEIPT : self::LINE_SPACING_PAYMENT_PART);
    }

    private function setPlaceholderElement(Placeholder $element): void
    {
        $type = $element->getType();

        switch ($type) {
            case Placeholder::PLACEHOLDER_TYPE_AMOUNT['type']:
                $y = $this->mpdf->GetY() + 1;
                $x = $this->mpdf->GetX() - 2;
                break;
            case Placeholder::PLACEHOLDER_TYPE_AMOUNT_RECEIPT['type']:
                $y = $this->mpdf->GetY() - 2;
                $x = $this->mpdf->GetX() + 11;
                break;
            case Placeholder::PLACEHOLDER_TYPE_PAYABLE_BY['type']:
            case Placeholder::PLACEHOLDER_TYPE_PAYABLE_BY_RECEIPT['type']:
            default:
                $y = $this->mpdf->GetY() + 1;
                $x = $this->mpdf->GetX() + 1;
        }

        $this->mpdf->Image(
            $element->getFile(Placeholder::FILE_TYPE_PNG),
            $x,
            $y,
            $element->getWidth(),
            $element->getHeight()
        );
    }

    private function setX(float $x) : void
    {
        $this->mpdf->SetX($x + $this->offsetX);
    }

    private function setY(float $y) : void
    {
        $this->mpdf->SetY($y + $this->offsetY);
    }

    private function SetXY(float $x, float $y) : void
    {
        $this->mpdf->SetXY($x + $this->offsetX, $y + $this->offsetY);
    }
}
