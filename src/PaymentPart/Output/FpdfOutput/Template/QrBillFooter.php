<?php


namespace Sprain\SwissQrBill\PaymentPart\Output\FpdfOutput\Template;

use Fpdf\Fpdf;

class QrBillFooter extends Fpdf
{
    public function SetDash($black = null, $white = null)
    {
        if ($black !== null) {
            $s = sprintf('[%.3F %.3F] 0 d', $black * $this->k, $white * $this->k);
        } else {
            $s = '[] 0 d';
        }
        $this->_out($s);
    }
}
