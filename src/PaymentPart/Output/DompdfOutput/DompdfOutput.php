<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output\DompdfOutput;

use Sprain\SwissQrBill\PaymentPart\Output\AbstractOutput;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\HtmlOutput;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\SwissQrBill\QrBill;
use Dompdf\Dompdf;

final class DompdfOutput extends AbstractOutput
{
    private HtmlOutput $htmlOutput;
    private const FONT_UNICODE = 'zapfdingbats';
    private const FONT_UNICODE_CHAR_SCISSORS = '"';
    private const FONT_UNICODE_CHAR_DOWN_ARROW = 't';

    public function __construct(QrBill $qrBill, string $language) {
        parent::__construct($qrBill, $language);
        $this->htmlOutput = (new HtmlOutput($qrBill, $language));
    }

    public function getPaymentPart(): ?string
    {
        $options = $this->getDisplayOptions();

        $html = $this->htmlOutput
            ->setDisplayOptions($options)
            // SVG is not compatible with Dompdf for now
            ->setQrCodeImageFormat(QrCode::FILE_FORMAT_PNG)
            ->getPaymentPart();

        // add custom styles
        $html .= $this->getTemplate();

        // replace base HTML special chars with the Dompdf-compatible ones
        $mapping = [
            '\\2702' => self::FONT_UNICODE_CHAR_SCISSORS,
            '\\25BC' => self::FONT_UNICODE_CHAR_DOWN_ARROW,
            '&#9986;' => self::FONT_UNICODE_CHAR_SCISSORS
        ];
        $html = str_replace(array_keys($mapping), array_values($mapping), $html);

        return $html;
    }


    private function getTemplate() {
        $options = $this->getDisplayOptions();

        $font = self::FONT_UNICODE;
        $scissorsLeft = $options->isPositionScissorsAtBottom() ? '2.6mm' : '-0.9mm';

        return <<<EOT
<style type="text/css">
    html {
        margin: 0;
    }
    #qr-bill-separate-info:before,
    #qr-bill-separate-info-text:before,
    #qr-bill-separate-info-text:after,
    #qr-bill #qr-bill-scissors {
        font-family: $font !important;
    }
    #qr-bill-separate-info:before {
        top: 3.0mm;
    }
    #qr-bill-scissors {
        left: $scissorsLeft;
    }
    #qr-bill {
        position: absolute;
        bottom: 104mm;
    }
    #qr-bill-currency {
        float: none !important;
        display: inline-block;
    }
    #qr-bill-amount {
        display: inline-block;
    }
</style>
EOT;
    }
}
