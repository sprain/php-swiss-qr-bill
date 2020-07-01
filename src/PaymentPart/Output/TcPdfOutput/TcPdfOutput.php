<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\TcPdfOutput;

use Sprain\SwissQrBill\PaymentPart\Output\AbstractOutput;
use Sprain\SwissQrBill\PaymentPart\Output\Element\OutputElementInterface;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Placeholder;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Text;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Title;
use Sprain\SwissQrBill\PaymentPart\Output\OutputInterface;
use Sprain\SwissQrBill\QrCode\Exception\UnsupportedFileExtensionException;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\SwissQrBill\PaymentPart\Translation\Translation;
use Sprain\SwissQrBill\QrBill;
use TCPDF;

final class TcPdfOutput extends AbstractOutput implements OutputInterface
{
    // TCPDF constants
    private const TCPDF_BORDER = 0;
    private const TCPDF_ALIGN_BELOW = 2;
    private const TCPDF_ALIGN_LEFT = 'L';
    private const TCPDF_ALIGN_RIGHT = 'R';
    private const TCPDF_ALIGN_CENTER = 'C';
    private const TCPDF_FONT = 'Helvetica';

    // Ratio constants
    private const TCPDF_LEFT_CELL_HEIGHT_RATIO_COMMON = 1.2;
    private const TCPDF_RIGHT_CELL_HEIGHT_RATIO_COMMON = 1.1;
    private const TCPDF_LEFT_CELL_HEIGHT_RATIO_CURRENCY_AMOUNT = 1.5;
    private const TCPDF_RIGHT_CELL_HEIGHT_RATIO_CURRENCY_AMOUNT = 1.5;

    // Location constants
    private const TCPDF_CURRENCY_AMOUNT_Y = 259;
    private const TCPDF_LEFT_PART_X = 4;
    private const TCPDF_RIGHT_PART_X = 66;
    private const TCPDF_RIGHT_PAR_X_INFO = 117;
    private const TCPDF_TITLE_Y = 195;

    // Font constants
    private const TCPDF_FONT_SIZE_TITLE_RECEIPT = 6;
    private const TCPDF_FONT_SIZE_RECEIPT = 8;

    private const TCPDF_FONT_SIZE_TITLE_PAYMENT_PART = 8;
    private const TCPDF_FONT_SIZE_PAYMENT_PART = 10;

    // Line spacing constants
    private const TCPDF_LINE_SPACING_RECEIPT = 3.5;
    private const TCPDF_LINE_SPACING_PAYMENT_PART = 4.8;

    /** @var  string */
    protected $language;

    /** @var QrBill */
    protected $qrBill;

    /* @var TCPDF $tcPdf */
    private $tcPdf;

    /* @var int $offsetX */
    private $offsetX;

    /* @var int $offsetY */
    private $offsetY;

    public function __construct(
        QrBill $qrBill,
        string $language,
        TCPDF $tcPdf,
        int $offsetX = 0,
        int $offsetY = 0
    ) {
        parent::__construct($qrBill, $language);
        $this->tcPdf = $tcPdf;
        $this->offsetX = $offsetX;
        $this->offsetY = $offsetY;
        $this->setQrCodeImageFormat(QrCode::FILE_FORMAT_SVG);
    }

    public function getPaymentPart(): void
    {
        $retainCellHeightRatio = $this->tcPdf->getCellHeightRatio();

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
    }

    private function addSwissQrCodeImage(): void
    {
        $qrCode = $this->getQrCode();

        switch ($this->getQrCodeImageFormat()) {
            case QrCode::FILE_FORMAT_SVG:
                $format = QrCode::FILE_FORMAT_SVG;
                $method = "ImageSVG";
                break;
            case QrCode::FILE_FORMAT_PNG:
            default:
                $format = QrCode::FILE_FORMAT_PNG;
                $method = "Image";
        }

        $yPosQrCode = 209.5 + $this->offsetY;
        $xPosQrCode = self::TCPDF_RIGHT_PART_X + 1 + $this->offsetX;

        $qrCode->setWriterByExtension($format);
        $img = base64_decode(preg_replace('#^data:image/[^;]+;base64,#', '', $qrCode->writeDataUri()));
        $this->tcPdf->$method("@".$img, $xPosQrCode, $yPosQrCode, 46, 46);
    }

    private function addInformationContentReceipt(): void
    {
        $x = self::TCPDF_LEFT_PART_X;
        $this->tcPdf->setCellHeightRatio(self::TCPDF_LEFT_CELL_HEIGHT_RATIO_COMMON);

        // Title
        $this->tcPdf->SetFont(self::TCPDF_FONT, 'B', 11);
        $this->SetY(self::TCPDF_TITLE_Y);
        $this->SetX($x);
        $this->printCell(Translation::get('receipt', $this->language), 0, 7);

        // Elements
        $this->SetY(204);
        foreach ($this->getInformationElementsOfReceipt() as $informationElement) {
            $this->SetX($x);
            $this->setContentElement($informationElement, true);
        }

        // Acceptance section
        $this->tcPdf->SetFont(self::TCPDF_FONT, 'B', 6);
        $this->SetY(273);
        $this->SetX($x);
        $this->printCell(Translation::get('acceptancePoint', $this->language), 54, 0, self::TCPDF_ALIGN_BELOW, self::TCPDF_ALIGN_RIGHT);
    }

    private function addInformationContent(): void
    {
        $x = self::TCPDF_RIGHT_PAR_X_INFO;
        $this->tcPdf->setCellHeightRatio(self::TCPDF_RIGHT_CELL_HEIGHT_RATIO_COMMON);

        // Title
        $this->tcPdf->SetFont(self::TCPDF_FONT, 'B', 11);
        $this->SetY(self::TCPDF_TITLE_Y);
        $this->SetX(self::TCPDF_RIGHT_PART_X);
        $this->printCell(Translation::get('paymentPart', $this->language), 48, 7);

        // Elements
        $this->SetY(197);
        foreach ($this->getInformationElements() as $informationElement) {
            $this->SetX($x);
            $this->setContentElement($informationElement, false);
        }
    }

    private function addCurrencyContentReceipt(): void
    {
        $x = self::TCPDF_LEFT_PART_X;
        $this->tcPdf->setCellHeightRatio(self::TCPDF_LEFT_CELL_HEIGHT_RATIO_CURRENCY_AMOUNT);
        $this->SetY(self::TCPDF_CURRENCY_AMOUNT_Y);

        foreach ($this->getCurrencyElements() as $currencyElement) {
            $this->SetX($x);
            $this->setContentElement($currencyElement, true);
        }
    }

    private function addAmountContentReceipt(): void
    {
        $x = 16;
        $this->tcPdf->setCellHeightRatio(self::TCPDF_LEFT_CELL_HEIGHT_RATIO_CURRENCY_AMOUNT);
        $this->SetY(self::TCPDF_CURRENCY_AMOUNT_Y);

        foreach ($this->getAmountElementsReceipt() as $amountElement) {
            $this->SetX($x);
            $this->setContentElement($amountElement, true);
        }
    }

    private function addCurrencyContent(): void
    {
        $x = self::TCPDF_RIGHT_PART_X;
        $this->tcPdf->setCellHeightRatio(self::TCPDF_RIGHT_CELL_HEIGHT_RATIO_CURRENCY_AMOUNT);
        $this->SetY(self::TCPDF_CURRENCY_AMOUNT_Y);

        foreach ($this->getCurrencyElements() as $currencyElement) {
            $this->SetX($x);
            $this->setContentElement($currencyElement, false);
        }
    }

    private function addAmountContent(): void
    {
        $x = 80;
        $this->tcPdf->setCellHeightRatio(self::TCPDF_RIGHT_CELL_HEIGHT_RATIO_CURRENCY_AMOUNT);
        $this->SetY(self::TCPDF_CURRENCY_AMOUNT_Y);

        foreach ($this->getAmountElements() as $amountElement) {
            $this->SetX($x);
            $this->setContentElement($amountElement, false);
        }
    }

    private function addFurtherInformationContent(): void
    {
        $x = self::TCPDF_RIGHT_PART_X;
        $this->tcPdf->setCellHeightRatio(self::TCPDF_RIGHT_CELL_HEIGHT_RATIO_COMMON);
        $this->SetY(286);
        $this->tcPdf->SetFont(self::TCPDF_FONT, '', 7);

        foreach ($this->getFurtherInformationElements() as $furtherInformationElement) {
            $this->SetX($x);
            $this->setContentElement($furtherInformationElement, true);
        }
    }

    private function addSeparatorContentIfNotPrintable(): void
    {
        if (!$this->isPrintable()) {
            $this->tcPdf->SetLineStyle(array('width' => 0.1, 'dash' => 4, 'color' => array(0, 0, 0)));
            $this->printLine(2, 193, 208, 193);
            $this->printLine(62, 193, 62, 296);
            $this->tcPdf->SetFont(self::TCPDF_FONT, '', 7);
            $this->SetY(188);
            $this->SetX(5);
            $this->printCell(Translation::get('separate', $this->language), 200, 0, 0, self::TCPDF_ALIGN_CENTER);
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
        $this->tcPdf->SetFont(
            self::TCPDF_FONT,
            'B',
            $isReceiptPart ? self::TCPDF_FONT_SIZE_TITLE_RECEIPT : self::TCPDF_FONT_SIZE_TITLE_PAYMENT_PART
        );
        $this->printCell(
            Translation::get(str_replace("text.", "", $element->getTitle()), $this->language),
            0,
            0,
            self::TCPDF_ALIGN_BELOW
        );
    }

    private function setTextElement(Text $element, bool $isReceiptPart): void
    {
        $this->tcPdf->SetFont(
            self::TCPDF_FONT,
            '',
            $isReceiptPart ? self::TCPDF_FONT_SIZE_RECEIPT : self::TCPDF_FONT_SIZE_PAYMENT_PART
        );

        $this->printMultiCell(
            str_replace("text.", "", $element->getText()),
            $isReceiptPart ? 54 : 0,
            0,
            self::TCPDF_ALIGN_BELOW,
            self::TCPDF_ALIGN_LEFT
        );
        $this->tcPdf->Ln($isReceiptPart ? self::TCPDF_LINE_SPACING_RECEIPT : self::TCPDF_LINE_SPACING_PAYMENT_PART);
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

    private function setX(int $x) : void
    {
        $this->tcPdf->SetX($x+$this->offsetX);
    }

    private function setY(int $y) : void
    {
        $this->tcPdf->SetY($y+$this->offsetY);
    }

    private function printCell(
        string $text,
        int $w = 0,
        int $h = 0,
        int $nextLineAlign = 0,
        string $textAlign = self::TCPDF_ALIGN_LEFT
    ) : void {
        $this->tcPdf->Cell($w, $h, $text, self::TCPDF_BORDER, $nextLineAlign, $textAlign);
    }

    private function printMultiCell(
        string $text,
        int $w = 0,
        int $h = 0,
        int $nextLineAlign = 0,
        string $textAlign = self::TCPDF_ALIGN_LEFT
    ) : void {
        $this->tcPdf->MultiCell($w, $h, $text, self::TCPDF_BORDER, $textAlign, false, $nextLineAlign);
    }

    private function printLine(int $x1, int $y1, int $x2, int $y2) : void
    {
        $this->tcPdf->Line($x1+$this->offsetX, $y1+$this->offsetY, $x2+$this->offsetX, $y2+$this->offsetY);
    }
}
