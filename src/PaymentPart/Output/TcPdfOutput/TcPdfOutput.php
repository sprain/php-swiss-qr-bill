<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output\TcPdfOutput;

use setasign\Fpdi\Tcpdf\Fpdi;
use Sprain\SwissQrBill\PaymentPart\Output\LineStyle;
use Sprain\SwissQrBill\PaymentPart\Output\VerticalSeparatorSymbolPosition;
use Sprain\SwissQrBill\PaymentPart\Output\AbstractOutput;
use Sprain\SwissQrBill\PaymentPart\Output\Element\FurtherInformation;
use Sprain\SwissQrBill\PaymentPart\Output\Element\OutputElementInterface;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Placeholder;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Text;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Title;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\SwissQrBill\PaymentPart\Translation\Translation;
use Sprain\SwissQrBill\QrBill;
use TCPDF;

final class TcPdfOutput extends AbstractOutput
{
    // TCPDF
    private const BORDER = 0;
    private const ALIGN_BELOW = 2;
    private const ALIGN_LEFT = 'L';
    private const ALIGN_RIGHT = 'R';
    private const ALIGN_CENTER = 'C';
    private const FONT = 'Helvetica';
    private const FONT_UNICODE = 'zapfdingbats';
    private const FONT_UNICODE_CHAR_SCISSORS = '"';
    private const FONT_UNICODE_CHAR_DOWN_ARROW = 't';

    // Ratio
    private const LEFT_CELL_HEIGHT_RATIO_COMMON = 1.2;
    private const RIGHT_CELL_HEIGHT_RATIO_COMMON = 1.1;
    private const LEFT_CELL_HEIGHT_RATIO_CURRENCY_AMOUNT = 1.5;
    private const RIGHT_CELL_HEIGHT_RATIO_CURRENCY_AMOUNT = 1.5;
    private const FURTHER_INFORMATION_CELL_HEIGHT_RATIO_COMMON = 1.5;

    // Positioning
    private const CURRENCY_AMOUNT_Y = 259;
    private const LEFT_PART_X = 4;
    private const RIGHT_PART_X = 66;
    private const RIGHT_PART_X_INFO = 117;
    private const TITLE_Y = 195;

    // Font
    private const FONT_SIZE_MAIN_TITLE = 11;
    private const FONT_SIZE_TITLE_RECEIPT = 6;
    private const FONT_SIZE_RECEIPT = 8;
    private const FONT_SIZE_TITLE_PAYMENT_PART = 8;
    private const FONT_SIZE_PAYMENT_PART = 10;
    private const FONT_SIZE_FURTHER_INFORMATION = 7;
    private const FONT_SIZE_SCISSORS = 14;
    private const FONT_SIZE_DOWN_ARROW = 10;

    // Line spacing
    private const LINE_SPACING_RECEIPT = 3.5;
    private const LINE_SPACING_PAYMENT_PART = 4.8;

    public function __construct(
        QrBill $qrBill,
        string $language,
        private TCPDF|Fpdi $tcPdf,
        private float $offsetX = 0,
        private float $offsetY = 0
    ) {
        parent::__construct($qrBill, $language);
        $this->setQrCodeImageFormat(QrCode::FILE_FORMAT_SVG);
    }

    public function getPaymentPart(): ?string
    {
        $retainCellHeightRatio = $this->tcPdf->getCellHeightRatio();
        $retainAutoPageBreak = $this->tcPdf->getAutoPageBreak();

        $this->tcPdf->SetAutoPageBreak(false);

        $this->addSeparatorContentIfNotPrintable();

        $this->addInformationContentReceipt();
        $this->addCurrencyContentReceipt();
        $this->addAmountContentReceipt();

        $this->addSwissQrCodeImage();
        $this->addInformationContent();
        $this->addCurrencyContent();
        $this->addAmountContent();
        $this->addFurtherInformationContent();

        $this->tcPdf->setCellHeightRatio($retainCellHeightRatio);
        $this->tcPdf->SetAutoPageBreak($retainAutoPageBreak);

        return null;
    }

    private function addSwissQrCodeImage(): void
    {
        $qrCode = $this->getQrCode();
        $qrCode->avoidCompactSvgs();

        $method = match ($this->getQrCodeImageFormat()) {
            QrCode::FILE_FORMAT_SVG => 'ImageSVG',
            default => 'Image',
        };

        $yPosQrCode = 209.5 + $this->offsetY;
        $xPosQrCode = self::RIGHT_PART_X + 1 + $this->offsetX;

        $img = $qrCode->getAsString($this->getQrCodeImageFormat());
        $this->tcPdf->$method('@'.$img, $xPosQrCode, $yPosQrCode, 46, 46);
    }

    private function addInformationContentReceipt(): void
    {
        $x = self::LEFT_PART_X;
        $this->tcPdf->setCellHeightRatio(self::LEFT_CELL_HEIGHT_RATIO_COMMON);

        // Title
        $this->tcPdf->SetFont($this->getFont(), 'B', self::FONT_SIZE_MAIN_TITLE);
        $this->setY(self::TITLE_Y);
        $this->setX($x);
        $this->printCell(Translation::get('receipt', $this->language), 0, 7);

        // Elements
        $this->setY(204);
        foreach ($this->getInformationElementsOfReceipt() as $informationElement) {
            $this->setX($x);
            $this->setContentElement($informationElement, true);
        }

        // Acceptance section
        $this->tcPdf->SetFont($this->getFont(), 'B', 6);
        $this->setY(273);
        $this->setX($x);
        $this->printCell(Translation::get('acceptancePoint', $this->language), 54, 0, self::ALIGN_BELOW, self::ALIGN_RIGHT);
    }

    private function addInformationContent(): void
    {
        $x = self::RIGHT_PART_X_INFO;
        $this->tcPdf->setCellHeightRatio(self::RIGHT_CELL_HEIGHT_RATIO_COMMON);

        // Title
        $this->tcPdf->SetFont($this->getFont(), 'B', self::FONT_SIZE_MAIN_TITLE);
        $this->setY(self::TITLE_Y);
        $this->setX(self::RIGHT_PART_X);
        $this->printCell(Translation::get('paymentPart', $this->language), 48, 7);

        // Elements
        $this->setY(197);
        foreach ($this->getInformationElements() as $informationElement) {
            $this->setX($x);
            $this->setContentElement($informationElement, false);
        }
    }

    private function addCurrencyContentReceipt(): void
    {
        $x = self::LEFT_PART_X;
        $this->tcPdf->setCellHeightRatio(self::LEFT_CELL_HEIGHT_RATIO_CURRENCY_AMOUNT);
        $this->setY(self::CURRENCY_AMOUNT_Y);

        foreach ($this->getCurrencyElements() as $currencyElement) {
            $this->setX($x);
            $this->setContentElement($currencyElement, true);
        }
    }

    private function addAmountContentReceipt(): void
    {
        $x = 16;
        $this->tcPdf->setCellHeightRatio(self::LEFT_CELL_HEIGHT_RATIO_CURRENCY_AMOUNT);
        $this->setY(self::CURRENCY_AMOUNT_Y);

        foreach ($this->getAmountElementsReceipt() as $amountElement) {
            $this->setX($x);
            $this->setContentElement($amountElement, true);
        }
    }

    private function addCurrencyContent(): void
    {
        $x = self::RIGHT_PART_X;
        $this->tcPdf->setCellHeightRatio(self::RIGHT_CELL_HEIGHT_RATIO_CURRENCY_AMOUNT);
        $this->setY(self::CURRENCY_AMOUNT_Y);

        foreach ($this->getCurrencyElements() as $currencyElement) {
            $this->setX($x);
            $this->setContentElement($currencyElement, false);
        }
    }

    private function addAmountContent(): void
    {
        $x = 80;
        $this->tcPdf->setCellHeightRatio(self::RIGHT_CELL_HEIGHT_RATIO_CURRENCY_AMOUNT);
        $this->setY(self::CURRENCY_AMOUNT_Y);

        foreach ($this->getAmountElements() as $amountElement) {
            $this->setX($x);
            $this->setContentElement($amountElement, false);
        }
    }

    private function addFurtherInformationContent(): void
    {
        $x = self::RIGHT_PART_X;
        $this->tcPdf->setCellHeightRatio(self::FURTHER_INFORMATION_CELL_HEIGHT_RATIO_COMMON);
        $this->setY(286);

        foreach ($this->getFurtherInformationElements() as $furtherInformationElement) {
            $this->setX($x);
            $this->setContentElement($furtherInformationElement, false);
        }
    }

    private function addSeparatorContentIfNotPrintable(): void
    {
        $layout = $this->getDisplayOptions();
        if ($layout->isPrintable()) {
            return;
        }

        $xstart = 2;
        $xmiddle = 62;
        $y = 193;
        $yend = 296;

        if ($layout->getLineStyle() !== LineStyle::NONE) {
            $lineStyle = ['width' => 0.1, 'color' => [0, 0, 0]];
            if ($layout->getLineStyle() === LineStyle::DASHED) {
                $lineStyle['dash'] = '2';
            }
            $this->tcPdf->SetLineStyle($lineStyle);
            $this->printLine($xstart, $y, 208, $y);
            $this->printLine($xmiddle, $y, $xmiddle, $yend);
        }

        if ($layout->isDisplayScissors()) {
            $this->tcPdf->setFont(self::FONT_UNICODE, '', self::FONT_SIZE_SCISSORS);
            // horizontal scissors
            $this->setY($y);
            $this->setX($xstart + 3);
            $this->tcPdf->Cell(0, 10, self::FONT_UNICODE_CHAR_SCISSORS, 0, 0, 'L', false, '', 0, false, 'C');
            // vertical scissors
            if ($layout->isPositionScissorsAtBottom()) {
                $this->setY($yend - 15);
                $this->setX($xmiddle);
                $this->tcPdf->StartTransform();
                $this->tcPdf->Rotate(90);
            } else {
                $this->setY($y + 3);
                $this->setX($xmiddle);
                $this->tcPdf->StartTransform();
                $this->tcPdf->Rotate(-90);
            }
            $this->tcPdf->Cell(0, 10, self::FONT_UNICODE_CHAR_SCISSORS, 0, 0, 'L', false, '', 0, false, 'C');
            $this->tcPdf->StopTransform();
        }

        if ($layout->isDisplayText()) {
            $this->tcPdf->SetFont($this->getFont(), '', self::FONT_SIZE_FURTHER_INFORMATION);
            $this->setY($y - 5);
            $this->setX($xstart + 3);
            $this->printCell(Translation::get('separate', $this->language), 200, 0, 0, self::ALIGN_CENTER);

            if ($layout->isDisplayTextDownArrows()) {
                $textWidth = $this->tcPdf->GetStringWidth(Translation::get('separate', $this->language));
                $arrowMargin = 3;
                $yoffset = 5.5;
                $this->tcPdf->SetFont(self::FONT_UNICODE, '', self::FONT_SIZE_DOWN_ARROW);

                $arrows = str_pad('', 3, self::FONT_UNICODE_CHAR_DOWN_ARROW);

                $xstart = ($this->tcPdf->getPageWidth() / 2) - ($textWidth / 2);
                $this->tcPdf->setXY($xstart - $arrowMargin, $y - $yoffset);
                $this->tcPdf->Cell(3, 1, $arrows, 0, 0, 'R', false);

                $xstart = ($this->tcPdf->getPageWidth() / 2) + ($textWidth / 2);
                $this->tcPdf->setXY($xstart, $y - $yoffset);
                $this->tcPdf->Cell(3, 1, $arrows, 0, 0, 'L', false);
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
        $this->tcPdf->SetFont(
            $this->getFont(),
            'B',
            $isReceiptPart ? self::FONT_SIZE_TITLE_RECEIPT : self::FONT_SIZE_TITLE_PAYMENT_PART
        );
        $this->printCell(
            Translation::get(str_replace('text.', '', $element->getTitle()), $this->language),
            0,
            0,
            self::ALIGN_BELOW
        );
    }

    private function setTextElement(Text $element, bool $isReceiptPart): void
    {
        $this->tcPdf->SetFont(
            $this->getFont(),
            '',
            $isReceiptPart ? self::FONT_SIZE_RECEIPT : self::FONT_SIZE_PAYMENT_PART
        );

        $this->printMultiCell(
            str_replace('text.', '', $element->getText()),
            $isReceiptPart ? 54 : 0,
            self::ALIGN_BELOW
        );

        $this->tcPdf->Ln($isReceiptPart ? self::LINE_SPACING_RECEIPT : self::LINE_SPACING_PAYMENT_PART);
    }

    private function setFurtherInformationElement(FurtherInformation $element): void
    {
        $this->tcPdf->SetFont($this->getFont(), '', self::FONT_SIZE_FURTHER_INFORMATION);
        $this->printMultiCell(
            $element->getText(),
            0,
            self::BORDER
        );
    }

    private function setPlaceholderElement(Placeholder $element): void
    {
        $type = $element->getType();

        switch ($type) {
            case Placeholder::PLACEHOLDER_TYPE_AMOUNT['type']:
                $y = $this->tcPdf->GetY() + 1;
                $x = $this->tcPdf->GetX() - 2;
                break;
            case Placeholder::PLACEHOLDER_TYPE_AMOUNT_RECEIPT['type']:
                $y = $this->tcPdf->GetY() - 2;
                $x = $this->tcPdf->GetX() + 11;
                break;
            case Placeholder::PLACEHOLDER_TYPE_PAYABLE_BY['type']:
            case Placeholder::PLACEHOLDER_TYPE_PAYABLE_BY_RECEIPT['type']:
            default:
                $y = $this->tcPdf->GetY() + 1;
                $x = $this->tcPdf->GetX() + 1;
        }

        $this->tcPdf->ImageSVG(
            $element->getFile(),
            $x,
            $y,
            $element->getWidth(),
            $element->getHeight()
        );
    }

    private function setX(int $x): void
    {
        $this->tcPdf->SetX($x+$this->offsetX);
    }

    private function setY(int $y): void
    {
        $this->tcPdf->SetY($y+$this->offsetY);
    }

    private function printCell(
        string $text,
        int $w = 0,
        int $h = 0,
        int $nextLineAlign = 0,
        string $textAlign = self::ALIGN_LEFT
    ): void {
        $this->tcPdf->Cell($w, $h, $text, self::BORDER, $nextLineAlign, $textAlign);
    }

    private function printMultiCell(
        string $text,
        int $w = 0,
        int $nextLineAlign = 0
    ): void {
        $this->tcPdf->MultiCell($w, 0, $text, self::BORDER, self::ALIGN_LEFT, false, $nextLineAlign);
    }

    private function printLine(int $x1, int $y1, int $x2, int $y2): void
    {
        $this->tcPdf->Line($x1+$this->offsetX, $y1+$this->offsetY, $x2+$this->offsetX, $y2+$this->offsetY);
    }

    private function getFont(): string
    {
        return self::FONT;
    }
}
