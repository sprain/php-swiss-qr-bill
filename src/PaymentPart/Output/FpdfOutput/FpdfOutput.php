<?php declare(strict_types=1);


namespace Sprain\SwissQrBill\PaymentPart\Output\FpdfOutput;

use Fpdf\Fpdf;
use setasign\Fpdi\Fpdi;
use Sprain\SwissQrBill\Exception\InvalidFpdfImageFormat;
use Sprain\SwissQrBill\PaymentPart\Output\AbstractOutput;
use Sprain\SwissQrBill\PaymentPart\Output\Element\FurtherInformation;
use Sprain\SwissQrBill\PaymentPart\Output\Element\OutputElementInterface;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Placeholder;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Text;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Title;
use Sprain\SwissQrBill\PaymentPart\Output\LineStyle;
use Sprain\SwissQrBill\PaymentPart\Translation\Translation;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;

final class FpdfOutput extends AbstractOutput
{
    // FPDF
    private const BORDER = 0;
    private const ALIGN_LEFT = 'L';
    private const ALIGN_RIGHT = 'R';
    private const ALIGN_CENTER = 'C';
    private const FONT = 'Helvetica';
    private const FONT_UNICODE = 'zapfdingbats';
    private const FONT_UNICODE_CHAR_SCISSORS = '"';
    private const FONT_UNICODE_CHAR_DOWN_ARROW = 't';

    // Positioning
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
    private const FONT_SIZE_SCISSORS = 14;
    private const FONT_SIZE_DOWN_ARROW = 10;

    // Line spacing
    private const LINE_SPACING_RECEIPT = 3.4;
    private const LINE_SPACING_PAYMENT_PART = 4.8;
    private float $amountLS = 0;

    public function __construct(
        QrBill $qrBill,
        string $language,
        private readonly Fpdf|Fpdi $fpdf,
        private readonly float $offsetX = 0,
        private readonly float $offsetY = 0
    ) {
        parent::__construct($qrBill, $language);
        $this->setQrCodeImageFormat(QrCode::FILE_FORMAT_PNG);
    }

    public function getPaymentPart(): ?string
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

        return null;
    }

    public function setQrCodeImageFormat(string $fileExtension): static
    {
        if (QrCode::FILE_FORMAT_SVG === $fileExtension) {
            throw new InvalidFpdfImageFormat('SVG images are not allowed by FPDF.');
        }

        $this->qrCodeImageFormat = $fileExtension;

        return $this;
    }

    private function addSwissQrCodeImage(): void
    {
        $qrCode = $this->getQrCode();

        $yPosQrCode = 209.5 + $this->offsetY;
        $xPosQrCode = 67 + $this->offsetX;

        if (ini_get('allow_url_fopen')) {
            $this->fpdf->Image(
                $qrCode->getDataUri($this->getQrCodeImageFormat()),
                $xPosQrCode,
                $yPosQrCode,
                46,
                46,
                'png'
            );

            return;
        }

        if (method_exists($this->fpdf, 'MemImage')) {
            $this->fpdf->MemImage(
                $qrCode->getAsString($this->getQrCodeImageFormat()),
                $xPosQrCode,
                $yPosQrCode,
                46,
                46
            );

            return;
        }

        throw new UnsupportedEnvironmentException(
            '"allow_url_fopen" is disabled on your server. Use FPDF with MemImageTrait. See fpdf-example.php within this library.'
        );
    }

    private function addInformationContentReceipt(): void
    {
        // Title
        $this->fpdf->SetFont($this->getFont(), 'B', self::FONT_SIZE_MAIN_TITLE);
        $this->SetXY(self::LEFT_PART_X, self::TITLE_Y);
        $this->fpdf->MultiCell(0, 7, $this->convertEncoding(Translation::get('receipt', $this->language)));

        // Elements
        $this->setY(204);
        foreach ($this->getInformationElementsOfReceipt() as $receiptInformationElement) {
            $this->setX(self::LEFT_PART_X);
            $this->setContentElement($receiptInformationElement, true);
        }

        // Acceptance section
        $this->fpdf->SetFont($this->getFont(), 'B', self::FONT_SIZE_TITLE_RECEIPT);
        $this->SetXY(self::LEFT_PART_X, 274.3);
        $this->fpdf->Cell(54, 0, $this->convertEncoding(Translation::get('acceptancePoint', $this->language)), self::BORDER, '', self::ALIGN_RIGHT);
    }

    private function addInformationContent(): void
    {
        // Title
        $this->fpdf->SetFont($this->getFont(), 'B', self::FONT_SIZE_MAIN_TITLE);
        $this->SetXY(self::RIGHT_PART_X, 195.2);
        $this->fpdf->MultiCell(48, 7, $this->convertEncoding(Translation::get('paymentPart', $this->language)));

        // Elements
        $this->setY(197.3);
        foreach ($this->getInformationElements() as $informationElement) {
            $this->setX(self::RIGHT_PART_X_INFO);
            $this->setContentElement($informationElement, false);
        }
    }

    private function addCurrencyContentReceipt(): void
    {
        $this->setY(self::CURRENCY_AMOUNT_Y);
        foreach ($this->getCurrencyElements() as $receiptCurrencyElement) {
            $this->amountLS = self::AMOUNT_LINE_SPACING_RCPT;
            $this->setX(self::LEFT_PART_X);
            $this->setContentElement($receiptCurrencyElement, true);
            $this->amountLS = 0;
        }
    }

    private function addAmountContentReceipt(): void
    {
        $this->setY(self::CURRENCY_AMOUNT_Y);
        foreach ($this->getAmountElementsReceipt() as $receiptAmountElement) {
            $this->amountLS = self::AMOUNT_LINE_SPACING_RCPT;
            $this->setX(16);
            $this->setContentElement($receiptAmountElement, true);
            $this->amountLS = 0;
        }
    }

    private function addCurrencyContent(): void
    {
        $this->setY(self::CURRENCY_AMOUNT_Y);
        foreach ($this->getCurrencyElements() as $currencyElement) {
            $this->amountLS = self::AMOUNT_LINE_SPACING;
            $this->setX(self::RIGHT_PART_X);
            $this->setContentElement($currencyElement, false);
            $this->amountLS = 0;
        }
    }

    private function addAmountContent(): void
    {
        $this->setY(self::CURRENCY_AMOUNT_Y);
        foreach ($this->getAmountElements() as $amountElement) {
            $this->amountLS = self::AMOUNT_LINE_SPACING;
            $this->setX(80);
            $this->setContentElement($amountElement, false);
            $this->amountLS = 0;
        }
    }

    private function addFurtherInformationContent(): void
    {
        $this->SetXY(self::RIGHT_PART_X, 286);

        foreach ($this->getFurtherInformationElements() as $furtherInformationElement) {
            $this->setX(self::RIGHT_PART_X);
            $this->setContentElement($furtherInformationElement, false);
        }
    }

    private function addSeparatorContentIfNotPrintable(): void
    {
        $layout = $this->getDisplayOptions();
        if ($layout->isPrintable()) {
            return;
        }

        if ($layout->getLineStyle() !== LineStyle::NONE) {
            $this->fpdf->SetLineWidth(0.1);
            if ($layout->getLineStyle() === LineStyle::DASHED) {
                if (!method_exists($this->fpdf, 'swissQrBillSetDash')) {
                    throw new MissingTraitException('Missing FpdfTrait in this fpdf instance. See fpdf-example.php within this library.');
                }
                $this->fpdf->swissQrBillSetDash(0.6, 0.6);
            }
            $this->fpdf->Line(2 + $this->offsetX, 193 + $this->offsetY, 208 + $this->offsetX, 193 + $this->offsetY);
            $this->fpdf->Line(62 + $this->offsetX, 193 + $this->offsetY, 62 + $this->offsetX, 296 + $this->offsetY);
            if ($layout->getLineStyle() === LineStyle::DASHED && method_exists($this->fpdf, 'swissQrBillSetDash')) {
                $this->fpdf->swissQrBillSetDash(0);
            }
        }

        if ($layout->isDisplayScissors()) {
            $this->fpdf->SetFont(self::FONT_UNICODE, '', self::FONT_SIZE_SCISSORS);

            // horizontal scissors
            $this->setXY(2 + $this->offsetX + 5, 193 + $this->offsetY + 0.2);
            $this->fpdf->Cell(1, 0, self::FONT_UNICODE_CHAR_SCISSORS, 0, 0, 'C');

            // vertical scissors
            if (!method_exists($this->fpdf, 'swissQrBillTextWithRotation')) {
                throw new MissingTraitException('Missing FpdfTrait in this fpdf instance. See fpdf-example.php within this library.');
            }
            if ($layout->isPositionScissorsAtBottom()) {
                $this->fpdf->swissQrBillTextWithRotation(62 + $this->offsetX + 1.7, 193 + $this->offsetY + 90, self::FONT_UNICODE_CHAR_SCISSORS, 90);
            } else {
                $this->fpdf->swissQrBillTextWithRotation(62 + $this->offsetX - 1.7, 193 + $this->offsetY + 4, self::FONT_UNICODE_CHAR_SCISSORS, -90);
            }
        }

        if ($layout->isDisplayText()) {
            $this->fpdf->SetFont($this->getFont(), '', self::FONT_SIZE_FURTHER_INFORMATION);
            $y = 189.6;
            $this->setY($y);
            $separateText = $this->convertEncoding(Translation::get('separate', $this->language));
            $this->fpdf->MultiCell(0, 0, $separateText, self::BORDER, self::ALIGN_CENTER);

            if ($layout->isDisplayTextDownArrows()) {
                $textWidth = $this->fpdf->GetStringWidth($separateText);
                $arrowMargin = 3;
                $yoffset = 0.6;
                $this->fpdf->SetFont(self::FONT_UNICODE, '', self::FONT_SIZE_DOWN_ARROW);

                $arrows = str_pad('', 3, self::FONT_UNICODE_CHAR_DOWN_ARROW);

                $xstart = ($this->fpdf->GetPageWidth() / 2) - ($textWidth / 2);
                $this->fpdf->setXY($xstart - $arrowMargin, $y - $yoffset);
                $this->fpdf->Cell(3, 1, $arrows, 0, 0, 'R', false);

                $xstart = ($this->fpdf->getPageWidth() / 2) + ($textWidth / 2);
                $this->fpdf->setXY($xstart, $y - $yoffset);
                $this->fpdf->Cell(3, 1, $arrows, 0, 0, 'L', false);
            }
        }
    }

    private function setContentElement(OutputElementInterface $element, bool $isReceiptPart): void
    {
        if ($element instanceof FurtherInformation) {
            $this->setFurtherInformationElement($element);
        }

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
        $this->fpdf->SetFont($this->getFont(), 'B', $isReceiptPart ? self::FONT_SIZE_TITLE_RECEIPT : self::FONT_SIZE_TITLE_PAYMENT_PART);
        $this->fpdf->MultiCell(
            0,
            2.8,
            $this->convertEncoding(
                Translation::get(str_replace('text.', '', $element->getTitle()), $this->language)
            )
        );
        $this->fpdf->Ln($this->amountLS);
    }

    private function setTextElement(Text $element, bool $isReceiptPart): void
    {
        $this->fpdf->SetFont($this->getFont(), '', $isReceiptPart ? self::FONT_SIZE_RECEIPT : self::FONT_SIZE_PAYMENT_PART);
        $this->fpdf->MultiCell(
            $isReceiptPart ? 54 : 0,
            $isReceiptPart ? 3.3 : 4,
            str_replace('text.', '', $this->convertEncoding($element->getText())),
            self::BORDER,
            self::ALIGN_LEFT
        );
        $this->fpdf->Ln($isReceiptPart ? self::LINE_SPACING_RECEIPT : self::LINE_SPACING_PAYMENT_PART);
    }

    private function setFurtherInformationElement(FurtherInformation $element): void
    {
        $this->fpdf->SetFont($this->getFont(), '', self::FONT_SIZE_FURTHER_INFORMATION);
        $this->fpdf->MultiCell(
            0,
            4,
            $this->convertEncoding($element->getText()),
            self::BORDER,
            self::ALIGN_LEFT
        );
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

    private function convertEncoding(string $text): string
    {
        // FPDF does not support unicode.
        return mb_convert_encoding($text, 'CP1252', 'UTF-8');
    }

    private function getFont(): string
    {
        return self::FONT;
    }
}
