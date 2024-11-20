<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output\DompdfOutput;

use Sprain\SwissQrBill\PaymentPart\Output\AbstractOutput;
use Sprain\SwissQrBill\PaymentPart\Output\DisplayOptions;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\HtmlOutput;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\SwissQrBill\QrBill;
use Dompdf\Dompdf;

final class DompdfOutput extends AbstractOutput
{
    private Dompdf $dompdf;
    private HtmlOutput $htmlOutput;
    private string $baseHtml;

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

        $html = $this->htmlOutput
            ->setDisplayOptions($this->getDisplayOptions())
            ->setQrCodeImageFormat($this->getQrCodeImageFormat())
            ->getPaymentPart();

        // add custom styles
        $html .= <<<EOT
        <p>&U2702;✂</p>
        <style type="text/css">
            html {
                margin: 0;
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

        $this->dompdf->loadHtml(str_replace(['<qrbill />', '<qrbill/>', '<qrbill></qrbill>'], $html, $this->baseHtml));

        return null;
    }
}
