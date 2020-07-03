<?php


namespace Sprain\SwissQrBill\PaymentPart\Output\FpdfOutput;

use Sprain\SwissQrBill\PaymentPart\Output\AbstractOutput;
use Sprain\SwissQrBill\PaymentPart\Output\Element\OutputElementInterface;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Placeholder;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Text;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Title;
use Sprain\SwissQrBill\PaymentPart\Output\FpdfOutput\Template\QrBillFooter;
use Sprain\SwissQrBill\PaymentPart\Output\OutputInterface;
use Sprain\SwissQrBill\PaymentPart\Translation\Translation;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;

final class FpdfOutput extends AbstractOutput implements OutputInterface
{
    /** @var QrBillFooter */
    private $fpdf;

    /** @var float */
    private $amountLS = 0;

    public function __construct(
        QrBill $qrBill,
        string $language,
        QrBillFooter $fpdf
    ) {
        parent::__construct($qrBill, $language);
        $this->fpdf = $fpdf;
        $this->setQrCodeImageFormat(QrCode::FILE_FORMAT_PNG);
    }

    public function getPaymentPart()
    {
        $this->fpdf->SetAutoPageBreak(false);
        $this->addSeparatorContentIfNotPrintable();
        $this->addReceiptPart();
        $this->addPaymentPart();
    }

    private function addSeparatorContentIfNotPrintable()
    {
        if (!$this->isPrintable()) {
            $this->fpdf->SetLineWidth(0.1);
            $this->fpdf->SetDash(1.25, 1.25);
            $this->fpdf->Line(2, 193, 208, 193);
            $this->fpdf->Line(62, 193, 62, 296);
            $this->fpdf->SetFont('helvetica', '', 7);
            $this->fpdf->SetY(189.6);
            $this->fpdf->MultiCell(0, 0, utf8_decode(Translation::get('separate', $this->language)), '', 'C');
        }
    }

    private function addReceiptPart()
    {
        // Title
        $this->fpdf->SetFont('helvetica', 'B', 11);
        $this->fpdf->SetXY(4, 195.2);
        $this->fpdf->MultiCell(0, 7, utf8_decode(Translation::get('receipt', $this->language)));

        // Elements
        $this->fpdf->SetY(204);
        foreach ($this->getInformationElementsOfReceipt() as $receiptInformationElement) {
            $this->fpdf->SetX(4);
            $this->setContentElement($receiptInformationElement, true);
        }

        // Amount
        $this->fpdf->SetY(259.3);
        foreach ($this->getCurrencyElements() as $receiptCurrencyElement) {
            $this->amountLS = 0.6;
            $this->fpdf->SetX(4);
            $this->setContentElement($receiptCurrencyElement, true);
            $this->amountLS = 0;
        }
        $this->fpdf->SetY(259.3);
        foreach ($this->getAmountElementsReceipt() as $receiptAmountElement) {
            $this->amountLS = 0.6;
            $this->fpdf->SetX(16);
            $this->setContentElement($receiptAmountElement, true);
            $this->amountLS = 0;
        }

        // Acceptance section
        $this->fpdf->SetFont('helvetica', 'B', 6);
        $this->fpdf->SetXY(4, 274.3);
        $this->fpdf->Cell(54, 0, utf8_decode(Translation::get('acceptancePoint', $this->language)), '', '', 'R');
    }

    private function addPaymentPart()
    {
        // Title
        $this->fpdf->SetFont('helvetica', 'B', 11);
        $this->fpdf->SetXY(66, 195.2);
        $this->fpdf->MultiCell(48, 7, utf8_decode(Translation::get('paymentPart', $this->language)));

        // QRCode
        $image = $this->getPngImage();
        $this->fpdf->Image($image[0], 67, 209.5, 46, 46, $image[1]);

        // Information Section
        $this->fpdf->SetY(197.3);
        foreach ($this->getInformationElements() as $informationElement) {
            $this->fpdf->SetX(117);
            $this->setContentElement($informationElement, false);
        }

        // Amount section
        $this->fpdf->SetY(260);
        foreach ($this->getCurrencyElements() as $currencyElement) {
            $this->amountLS = 1.2;
            $this->fpdf->SetX(66);
            $this->setContentElement($currencyElement, false);
            $this->amountLS = 0;
        }
        $this->fpdf->SetY(260);
        foreach ($this->getAmountElements() as $amountElement) {
            $this->amountLS = 1.2;
            $this->fpdf->SetX(80);
            $this->setContentElement($amountElement, false);
            $this->amountLS = 0;
        }

        // Further Information Section
        $this->fpdf->SetY(286);
        $this->fpdf->SetFont('helvetica', '', 7);
        foreach ($this->getFurtherInformationElements() as $furtherInformationElement) {
            $this->fpdf->SetX(66);
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
        $this->fpdf->SetFont('helvetica', 'B', $isReceiptPart ? 6 : 8);
        $this->fpdf->MultiCell(0, 2.8, utf8_decode(
            Translation::get(str_replace("text.", "", $element->getTitle()), $this->language)
        ));
        $this->fpdf->Ln($this->amountLS);
    }

    private function setTextElement(Text $element, bool $isReceiptPart): void
    {
        $this->fpdf->SetFont('helvetica', '', $isReceiptPart ? 8 : 10);
        $this->fpdf->MultiCell(
            $isReceiptPart ? 54 : 0,
            $isReceiptPart ? 3.3 : 4,
            str_replace("text.", "", utf8_decode($element->getText())),
            '',
            'L'
        );
        $this->fpdf->Ln($isReceiptPart ? 3.4 : 4.8);
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
