<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output\FpdfOutput;

/**
 * @internal
 */
trait FpdfTrait
{
    /**
     * Set dash line style for the next ->Line() command
     *
     * @see http://www.fpdf.org/en/script/script33.php
     */
    public function swissQrBillSetDash(float $black = 0, ?float $white = null): void
    {
        $white = $white === null ? $black : $white;

        $s = $black > 0
            ? sprintf('[%.3F %.3F] 0 d', $black * $this->k, $white * $this->k)
            : '[] 0 d';

        $this->_out($s);
    }

    /**
     * Rotate text
     *
     * @see http://www.fpdf.org/en/script/script31.php
     */
    public function swissQrBillTextWithRotation(float $x, float $y, string $txt, float $txtAngle = 0, float $fontAngle = 0): void
    {
        $fontAngle += 90 + $txtAngle;
        $txtAngle *= M_PI / 180;
        $fontAngle *= M_PI / 180;

        $txt_dx = cos($txtAngle);
        $txt_dy = sin($txtAngle);
        $font_dx = cos($fontAngle);
        $font_dy = sin($fontAngle);

        $s = sprintf(
            'BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',
            $txt_dx,
            $txt_dy,
            $font_dx,
            $font_dy,
            $x * $this->k,
            ($this->h-$y) * $this->k,
            $this->_escape($txt)
        );

        if ($this->ColorFlag) {
            $s = sprintf('q %s %s Q', $this->TextColor, $s);
        }
        $this->_out($s);
    }
}
