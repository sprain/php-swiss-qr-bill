<?php


namespace Sprain\SwissQrBill\PaymentPart\FpdfOutput;


use Fpdf\Fpdf;
use Sprain\SwissQrBill\PaymentPart\Translation\Translation;
use Sprain\SwissQrBill\QrBill;

class BillFooter extends Fpdf
{
    /** @var  string */
    private $language;

    /** @var QrBill */
    private $qrBill;

    /**
     * BillFooter constructor.
     * @param string|null $orientation
     * @param string|null $unit
     * @param string|null $size
     * @param QrBill $qrBill
     * @param string|null $language
     */
    public function __construct( QrBill $qrBill, ?string $orientation = 'P', ?string $unit = 'mm', ?string $size = 'A4', ?string $language = 'fr')
    {
        parent::__construct($orientation, $unit, $size);
        $this->qrBill = $qrBill;
        $this->language = $language;
    }

    public function billFooter()
    {
        $this->getLines();
        $this->getReceiptPart();
        $this->getPaymentPart();
    }
    private function getLines()
    {
        $this->SetAutoPageBreak(0.5);
        $this->SetLineWidth(0.1);
        $this->SetDash(1,1); //1mm on, 1mm off
        $this->Line(2, 191, 208, 191);
        $this->Line(62, 191, 62, 296);
        $this->SetFont('helvetica','',8);
        $this->SetY(189);
        $this->MultiCell(0,0, utf8_decode(Translation::get('separate', $this->language)),'', 'C');
    }
    private function SetDash($black = null, $white = null)
    {
        if($black !== null)
            $s = sprintf('[%.3F %.3F] 0 d',$black*$this->k,$white * $this->k);
        else
            $s = '[] 0 d';
        $this->_out($s);
    }

    /** Receipt part */
    private function getReceiptPart()
    {
        /** Title */
        $this->SetFont('helvetica','B',11);
        $this->SetXY(4, 194.5);
        $this->MultiCell(52,7, utf8_decode(Translation::get('receipt', $this->language)));

        /** Information Creditor Section */
        $this->SetFont('helvetica','B',6);
        $this->SetXY(4, 204);
        $this->MultiCell(0,0, utf8_decode(Translation::get('creditor', $this->language)));
        $this->SetFont('helvetica','',8);
        $this->Ln(3);
        $this->SetX(4);
        $this->MultiCell(0,0, $this->qrBill->getCreditorInformation()->getFormattedIban());
        $this->Ln(2.2);
        $this->SetX(4);
        $creditor = nl2br($this->qrBill->getCreditor()->getFullAddress());
        $creditor = str_replace('<br />', "\n", $creditor);
        $this->MultiCell(0,1.6, utf8_decode($creditor));

        /** Information Reference section */
        $this->SetFont('helvetica','B',6);
        $this->Ln(3.4);
        $this->Ln(3.4);
        $this->SetX(4);
        $this->Cell(0,0, utf8_decode(Translation::get('reference', $this->language)));
        $this->SetFont('helvetica','',8);
        $this->Ln(3);
        $this->SetX(4);
        $this->Cell(0,0, utf8_decode($this->qrBill->getPaymentReference()->getFormattedReference()));

        /** Information Debtor Section */
        $this->SetFont('helvetica','B',6);
        $this->Ln(3.3);
        $this->Ln(3.3);
        $this->SetX(4);
        $this->Cell(0,0, utf8_decode(Translation::get('payableBy', $this->language)));
        $this->SetFont('helvetica','',8);
        $this->Ln(3);
        $this->SetX(4);
        $debtor = nl2br($this->qrBill->getUltimateDebtor()->getFullAddress());
        $debtor = str_replace('<br />', "\n", $debtor);
        $this->MultiCell(0,1.6, utf8_decode($debtor));

        /** Amount section */
        $this->SetFont('helvetica','B',6);
        $this->SetXY(4, 260.5);
        $this->Cell(0,0, utf8_decode(Translation::get('currency', $this->language)));
        $this->SetXY(16, 260.5);
        $this->Cell(0,0, utf8_decode(Translation::get('amount', $this->language)));
        $this->SetFont('helvetica','',8);
        $this->SetXY(4, 264.5);
        $this->Cell(0,0, 'CHF');
        $this->SetXY(16, 264.5);
        $this->Cell(0,0, $this->qrBill->getPaymentAmountInformation()->getAmount());

        /** Acceptance section */
        $this->SetFont('helvetica','B',6);
        $this->SetXY(38, 275);
        $this->Cell(0,0, utf8_decode(Translation::get('acceptancePoint', $this->language)));
    }

    private function getPaymentPart()
    {
        /** Title */
        $this->SetFont('helvetica','B',11);
        $this->SetXY(67, 194.5);
        $this->MultiCell(0,7, utf8_decode(Translation::get('paymentPart', $this->language)));

        /** QRCode */
        $this->Image('qr.png',68,208, 46, 46);

        /** Amount section */
        $this->SetFont('helvetica','B',8);
        $this->SetXY(67, 261);
        $this->Cell(0,0, utf8_decode(Translation::get('currency', $this->language)));
        $this->SetXY(82, 261);
        $this->Cell(0,0, utf8_decode(Translation::get('amount', $this->language)));
        $this->SetFont('helvetica','',10);
        $this->SetXY(67, 265.5);
        $this->Cell(0,0, 'CHF');
        $this->SetXY(82, 265.5);
        $this->Cell(0,0, $this->qrBill->getPaymentAmountInformation()->getAmount());

        /** Information Creditor Section */
        $this->SetFont('helvetica','B',8);
        $this->SetXY(118.5, 197.5);
        $this->MultiCell(0,0, utf8_decode(Translation::get('creditor', $this->language)));
        $this->SetFont('helvetica','',10);
        $this->Ln(3.5);
        $this->SetX(118.5);
        $this->MultiCell(0,0, $this->qrBill->getCreditorInformation()->getFormattedIban());
        $this->Ln(2.6);
        $this->SetX(118.5);
        $creditor = nl2br($this->qrBill->getCreditor()->getFullAddress());
        $creditor = str_replace('<br />', "\n", $creditor);
        $this->MultiCell(0,2, utf8_decode($creditor));

        /** Information Reference section */
        $this->SetFont('helvetica','B',8);
        $this->Ln(4);
        $this->Ln(4);
        $this->SetX(118.5);
        $this->Cell(0,0, utf8_decode(Translation::get('reference', $this->language)));
        $this->SetFont('helvetica','',10);
        $this->Ln(3.5);
        $this->SetX(118.5);
        $this->Cell(0,0, utf8_decode($this->qrBill->getPaymentReference()->getFormattedReference()));

        /** Information Debtor section */
        $this->SetFont('helvetica','B',8);
        $this->Ln(4);
        $this->Ln(4);
        $this->SetX(118.5);
        $this->Cell(0,0, utf8_decode(Translation::get('payableBy', $this->language)));
        $this->SetFont('helvetica','',10);
        $this->Ln(2.4);
        $this->SetX(118.5);
        $debtor = nl2br($this->qrBill->getUltimateDebtor()->getFullAddress());
        $debtor = str_replace('<br />', "\n", $debtor);
        $this->MultiCell(0,2, utf8_decode($debtor));
    }
}