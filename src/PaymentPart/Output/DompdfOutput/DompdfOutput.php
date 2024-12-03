<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output\DompdfOutput;

use Sprain\SwissQrBill\PaymentPart\Output\AbstractOutput;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\HtmlOutput;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\SwissQrBill\QrBill;
use Dompdf\Dompdf;

final class DompdfOutput extends AbstractOutput
{
    private Dompdf $dompdf;
    private HtmlOutput $htmlOutput;
    private string $baseHtml;

    private const FONT_UNICODE = 'DejaVu Sans, sans-serif';
    private const FONT_UNICODE_CHAR_SCISSORS = '✂';
    private const FONT_UNICODE_CHAR_DOWN_ARROW = '▼';

    public function __construct(
        QrBill $qrBill,
        string $language,
        Dompdf $dompdf,
        string $baseHtml = '<qrbill />'
    ) {
        parent::__construct($qrBill, $language);
        $this->dompdf = $dompdf;
        $this->baseHtml = $baseHtml;
        $this->htmlOutput = (new HtmlOutput($qrBill, $language));
    }

    public function getPaymentPart(): ?string
    {
        // SVG is not compatible with Dompdf for now
        $this->setQrCodeImageFormat(QrCode::FILE_FORMAT_PNG);

        $options = $this->getDisplayOptions();
        $html = $this->htmlOutput
            ->setDisplayOptions($options)
            ->setQrCodeImageFormat($this->getQrCodeImageFormat())
            ->getPaymentPart();

        // add custom styles
        $font = self::FONT_UNICODE;
        $scissorsLeft = $options->isPositionScissorsAtBottom() ? '2.6mm' : '-0.9mm';
        $html .= <<<EOT
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

        $data = str_ireplace(['<qrbill />', '<qrbill/>', '<qrbill></qrbill>'], $html, $this->baseHtml);
        $mapping = [
            '\\2702' => self::FONT_UNICODE_CHAR_SCISSORS,
            '\\25BC' => self::FONT_UNICODE_CHAR_DOWN_ARROW,
            '&#9986;' => self::FONT_UNICODE_CHAR_SCISSORS
        ];
        $data = str_replace(array_keys($mapping), array_values($mapping), $data);
        $this->dompdf->loadHtml($data, 'UTF-8');

        return null;
    }
}
