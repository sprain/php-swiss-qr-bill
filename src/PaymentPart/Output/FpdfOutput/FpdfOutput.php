<?php declare(strict_types=1);


namespace Sprain\SwissQrBill\PaymentPart\Output\FpdfOutput;

use Fpdf\Fpdf;
use Sprain\SwissQrBill\Exception\InvalidFpdfImageFormat;
use Sprain\SwissQrBill\PaymentPart\Output\AbstractOutput;
use Sprain\SwissQrBill\PaymentPart\Output\Element\OutputElementInterface;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Placeholder;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Text;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Title;
use Sprain\SwissQrBill\PaymentPart\Output\OutputInterface;
use Sprain\SwissQrBill\PaymentPart\Translation\Translation;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;

final class FpdfOutput extends AbstractOutput implements OutputInterface
{
    // FPDF
    private const BORDER = 0;
    private const ALIGN_LEFT = 'L';
    private const ALIGN_RIGHT = 'R';
    private const ALIGN_CENTER = 'C';
    private const FONT = 'Helvetica';

    // Location
    private const CURRENCY_AMOUNT_Y = 259.3;
    private const AMOUNT_LINE_SPACING = 1.2;
    private const AMOUNT_LINE_SPACING_RCPT = 0.6;
    private const LEFT_PART_X = 4;
    private const RIGHT_PART_X = 66;
    private const RIGHT_PART_X_INFO = 117;
    private const TITLE_Y = 195.2;

    // Font size
    private const FONT_SIZE_MAIN_TITLE = 11;
    private const FONT_SIZE_TITLE_RECEIPT = 6;
    private const FONT_SIZE_RECEIPT = 8;
    private const FONT_SIZE_TITLE_PAYMENT_PART = 8;
    private const FONT_SIZE_PAYMENT_PART = 10;
    private const FONT_SIZE_FURTHER_INFORMATION = 7;

    // Line spacing
    private const LINE_SPACING_RECEIPT = 3.4;
    private const LINE_SPACING_PAYMENT_PART = 4.8;
    private float $amountLS = 0;

    private Fpdf $fpdf;
    private float $offsetX;
    private float $offsetY;

    public function __construct(
        QrBill $qrBill,
        string $language,
        Fpdf $fpdf,
        float $offsetX = 0,
        float $offsetY = 0
    ) {
        parent::__construct($qrBill, $language);
        $this->fpdf = $fpdf;
        $this->offsetX = $offsetX;
        $this->offsetY = $offsetY;
        $this->setQrCodeImageFormat(QrCode::FILE_FORMAT_PNG);
    }

    public function getPaymentPart()
    {
        $this->fpdf->SetAutoPageBreak(false);

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
            throw new InvalidFpdfImageFormat('SVG images are not allowed by FPDF.');
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

        $this->fpdf->Image($qrCode->writeDataUri(), $xPosQrCode, $yPosQrCode, 46, 46, 'png');
    }

    private function addInformationContentReceipt(): void
    {
        // Title
        $this->fpdf->SetFont(self::FONT, 'B', self::FONT_SIZE_MAIN_TITLE);
        $this->SetXY(self::LEFT_PART_X, self::TITLE_Y);
        $this->fpdf->MultiCell(0, 7, utf8_decode(Translation::get('receipt', $this->language)));

        // Elements
        $this->SetY(204);
        foreach ($this->getInformationElementsOfReceipt() as $receiptInformationElement) {
            $this->SetX(self::LEFT_PART_X);
            $this->setContentElement($receiptInformationElement, true);
        }

        // Acceptance section
        $this->fpdf->SetFont(self::FONT, 'B', self::FONT_SIZE_TITLE_RECEIPT);
        $this->SetXY(self::LEFT_PART_X, 274.3);
        $this->fpdf->Cell(54, 0, utf8_decode(Translation::get('acceptancePoint', $this->language)), self::BORDER, '', self::ALIGN_RIGHT);
    }

    private function addInformationContent(): void
    {
        // Title
        $this->fpdf->SetFont(self::FONT, 'B', self::FONT_SIZE_MAIN_TITLE);
        $this->SetXY(self::RIGHT_PART_X, 195.2);
        $this->fpdf->MultiCell(48, 7, utf8_decode(Translation::get('paymentPart', $this->language)));

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
        $this->fpdf->SetFont(self::FONT, '', self::FONT_SIZE_FURTHER_INFORMATION);

        foreach ($this->getFurtherInformationElements() as $furtherInformationElement) {
            $this->SetX(self::RIGHT_PART_X);
            $this->setContentElement($furtherInformationElement, true);
        }
    }

    private function addSeparatorContentIfNotPrintable()
    {
        if (!$this->isPrintable()) {
            $this->fpdf->SetLineWidth(0.1);
            $this->fpdf->Line(2 + $this->offsetX, 193 + $this->offsetY, 208 + $this->offsetX, 193 + $this->offsetY);
            $this->fpdf->Line(62 + $this->offsetX, 193 + $this->offsetY, 62 + $this->offsetX, 296 + $this->offsetY);
            $this->fpdf->SetFont(self::FONT, '', self::FONT_SIZE_FURTHER_INFORMATION);
            $this->SetY(189.6);
            $this->fpdf->MultiCell(0, 0, utf8_decode(Translation::get('separate', $this->language)), self::BORDER, self::ALIGN_CENTER);
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
        $this->fpdf->SetFont(self::FONT, 'B', $isReceiptPart ? self::FONT_SIZE_TITLE_RECEIPT : self::FONT_SIZE_TITLE_PAYMENT_PART);
        $this->fpdf->MultiCell(0, 2.8, utf8_decode(
            Translation::get(str_replace("text.", "", $element->getTitle()), $this->language)
        ));
        $this->fpdf->Ln($this->amountLS);
    }

    private function setTextElement(Text $element, bool $isReceiptPart): void
    {
        $this->fpdf->SetFont(self::FONT, '', $isReceiptPart ? self::FONT_SIZE_RECEIPT : self::FONT_SIZE_PAYMENT_PART);
        $this->fpdf->MultiCell(
            $isReceiptPart ? 54 : 0,
            $isReceiptPart ? 3.3 : 4,
            str_replace("text.", "", utf8_decode($element->getText())),
            self::BORDER,
            self::ALIGN_LEFT
        );
        $this->fpdf->Ln($isReceiptPart ? self::LINE_SPACING_RECEIPT : self::LINE_SPACING_PAYMENT_PART);
    }

    private function setPlaceholderElement(Placeholder $element): void
    {
        $type = $element->getType();

        switch ($type) {
            case Placeholder::PLACEHOLDER_TYPE_AMOUNT['type']:
                $y = $this->fpdf->GetY() + 1;
                $x = $this->fpdf->GetX() - 2;
                break;
            case Placeholder::PLACEHOLDER_TYPE_AMOUNT_RECEIPT['type']:
                $y = $this->fpdf->GetY() - 2;
                $x = $this->fpdf->GetX() + 11;
                break;
            case Placeholder::PLACEHOLDER_TYPE_PAYABLE_BY['type']:
            case Placeholder::PLACEHOLDER_TYPE_PAYABLE_BY_RECEIPT['type']:
            default:
                $y = $this->fpdf->GetY() + 1;
                $x = $this->fpdf->GetX() + 1;
        }

        $this->fpdf->Image(
            $element->getFile(Placeholder::FILE_TYPE_PNG),
            $x,
            $y,
            $element->getWidth(),
            $element->getHeight()
        );
    }

    private function setX(float $x): void
    {
        $this->fpdf->SetX($x + $this->offsetX);
    }

    private function setY(float $y): void
    {
        $this->fpdf->SetY($y + $this->offsetY);
    }

    private function SetXY(float $x, float $y): void
    {
        $this->fpdf->SetXY($x + $this->offsetX, $y + $this->offsetY);
    }
}
