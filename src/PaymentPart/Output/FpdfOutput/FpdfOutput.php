<?php


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
    // FPDF constants
    private const FPDF_BORDER = 0;
    private const FPDF_ALIGN_LEFT = 'L';
    private const FPDF_ALIGN_RIGHT = 'R';
    private const FPDF_ALIGN_CENTER = 'C';
    private const FPDF_FONT = 'Helvetica';

    // Location constants
    private const FPDF_CURRENCY_AMOUNT_Y = 259.3;
    private const FPDF_AMOUNT_LINE_SPACING = 1.2;
    private const FPDF_AMOUNT_LINE_SPACING_RCPT = 0.6;
    private const FPDF_LEFT_PART_X = 4;
    private const FPDF_RIGHT_PART_X = 66;
    private const FPDF_RIGHT_PAR_X_INFO = 117;
    private const FPDF_TITLE_Y = 195.2;

    // Line spacing constants
    private const FPDF_9PT = 3.4;
    private const FPDF_11PT = 4.8;
    
    /** @var Fpdf */
    private $fpdf;

    /** @var float */
    private $amountLS = 0;

    /* @var int $offsetX */
    private $offsetX;

    /* @var int $offsetY */
    private $offsetY;

    public function __construct(
        QrBill $qrBill,
        string $language,
        Fpdf $fpdf,
        int $offsetX = 0,
        int $offsetY = 0
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
        $this->addReceiptPart();
        $this->addPaymentPart();
    }

    /**
     * @param string $fileExtension
     * @return $this
     * @throws InvalidFpdfImageFormat
     */
    public function setQrCodeImageFormat(string $fileExtension): AbstractOutput
    {
        $this->qrCodeImageFormat = $fileExtension;
        if ($fileExtension === 'svg') {
            throw new InvalidFpdfImageFormat('SVG images are not allowed by FPDF.');
        }
        return $this;
    }

    private function addSeparatorContentIfNotPrintable()
    {
        if (!$this->isPrintable()) {
            $this->fpdf->SetLineWidth(0.1);
            $this->fpdf->Line(2, 193, 208, 193);
            $this->fpdf->Line(62, 193, 62, 296);
            $this->fpdf->SetFont(self::FPDF_FONT, '', 7);
            $this->SetY(189.6);
            $this->fpdf->MultiCell(0, 0, utf8_decode(Translation::get('separate', $this->language)), self::FPDF_BORDER, self::FPDF_ALIGN_CENTER);
        }
    }

    private function addReceiptPart()
    {
        // Title
        $this->fpdf->SetFont(self::FPDF_FONT, 'B', 11);
        $this->SetXY(self::FPDF_LEFT_PART_X, self::FPDF_TITLE_Y);
        $this->fpdf->MultiCell(0, 7, utf8_decode(Translation::get('receipt', $this->language)));

        // Elements
        $this->SetY(204);
        foreach ($this->getInformationElementsOfReceipt() as $receiptInformationElement) {
            $this->SetX(self::FPDF_LEFT_PART_X);
            $this->setContentElement($receiptInformationElement, true);
        }

        // Amount
        $this->SetY(self::FPDF_CURRENCY_AMOUNT_Y);
        foreach ($this->getCurrencyElements() as $receiptCurrencyElement) {
            $this->amountLS = self::FPDF_AMOUNT_LINE_SPACING_RCPT;
            $this->SetX(self::FPDF_LEFT_PART_X);
            $this->setContentElement($receiptCurrencyElement, true);
            $this->amountLS = 0;
        }
        $this->SetY(self::FPDF_CURRENCY_AMOUNT_Y);
        foreach ($this->getAmountElementsReceipt() as $receiptAmountElement) {
            $this->amountLS = self::FPDF_AMOUNT_LINE_SPACING_RCPT;
            $this->SetX(16);
            $this->setContentElement($receiptAmountElement, true);
            $this->amountLS = 0;
        }

        // Acceptance section
        $this->fpdf->SetFont(self::FPDF_FONT, 'B', 6);
        $this->SetXY(self::FPDF_LEFT_PART_X, 274.3);
        $this->fpdf->Cell(54, 0, utf8_decode(Translation::get('acceptancePoint', $this->language)), self::FPDF_BORDER, '', self::FPDF_ALIGN_RIGHT);
    }

    private function addPaymentPart()
    {
        // Title
        $this->fpdf->SetFont(self::FPDF_FONT, 'B', 11);
        $this->SetXY(self::FPDF_RIGHT_PART_X, 195.2);
        $this->fpdf->MultiCell(48, 7, utf8_decode(Translation::get('paymentPart', $this->language)));

        // QRCode
        $image = $this->getPngImage();
        $this->fpdf->Image($image[0], 67, 209.5, 46, 46, $image[1]);

        // Information Section
        $this->SetY(197.3);
        foreach ($this->getInformationElements() as $informationElement) {
            $this->SetX(self::FPDF_RIGHT_PAR_X_INFO);
            $this->setContentElement($informationElement, false);
        }

        // Amount section
        $this->SetY(self::FPDF_CURRENCY_AMOUNT_Y);
        foreach ($this->getCurrencyElements() as $currencyElement) {
            $this->amountLS = self::FPDF_AMOUNT_LINE_SPACING;
            $this->SetX(self::FPDF_RIGHT_PART_X);
            $this->setContentElement($currencyElement, false);
            $this->amountLS = 0;
        }
        $this->SetY(self::FPDF_CURRENCY_AMOUNT_Y);
        foreach ($this->getAmountElements() as $amountElement) {
            $this->amountLS = self::FPDF_AMOUNT_LINE_SPACING;
            $this->SetX(80);
            $this->setContentElement($amountElement, false);
            $this->amountLS = 0;
        }

        // Further Information Section
        $this->SetY(286);
        $this->fpdf->SetFont(self::FPDF_FONT, '', 7);
        foreach ($this->getFurtherInformationElements() as $furtherInformationElement) {
            $this->SetX(self::FPDF_RIGHT_PART_X);
            $this->setContentElement($furtherInformationElement, true);
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
        $this->fpdf->SetFont(self::FPDF_FONT, 'B', $isReceiptPart ? 6 : 8);
        $this->fpdf->MultiCell(0, 2.8, utf8_decode(
            Translation::get(str_replace("text.", "", $element->getTitle()), $this->language)
        ));
        $this->fpdf->Ln($this->amountLS);
    }

    private function setTextElement(Text $element, bool $isReceiptPart): void
    {
        $this->fpdf->SetFont(self::FPDF_FONT, '', $isReceiptPart ? 8 : 10);
        $this->fpdf->MultiCell(
            $isReceiptPart ? 54 : 0,
            $isReceiptPart ? 3.3 : 4,
            str_replace("text.", "", utf8_decode($element->getText())),
            self::FPDF_BORDER,
            self::FPDF_ALIGN_LEFT
        );
        $this->fpdf->Ln($isReceiptPart ? self::FPDF_9PT : self::FPDF_11PT);
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
            $element->getFile('png'),
            $x,
            $y,
            $element->getWidth(),
            $element->getHeight()
        );
    }

    private function setX(float $x) : void
    {
        $this->fpdf->SetX($x + $this->offsetX);
    }

    private function setY(float $y) : void
    {
        $this->fpdf->SetY($y + $this->offsetY);
    }

    private function SetXY(float $x, float $y) : void
    {
        $this->fpdf->SetXY($x + $this->offsetX, $y + $this->offsetY);
    }

    private function getPngImage()
    {
        $qrCode = $this->getQrCode();
        $format = QrCode::FILE_FORMAT_PNG;
        $qrCode->setWriterByExtension($format);
        $image64 = explode(',', $qrCode->writeDataUri(), 2);
        $image = 'data://text/plain;base64,' . $image64[1];
        $type = 'png';

        return [$image, $type];
    }
}
