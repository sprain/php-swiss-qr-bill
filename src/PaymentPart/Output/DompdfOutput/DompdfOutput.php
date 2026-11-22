<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output\DompdfOutput;

use Sprain\SwissQrBill\PaymentPart\Output\AbstractOutput;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\HtmlOutput;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\SwissQrBill\QrBill;

final class DompdfOutput extends AbstractOutput
{
    private HtmlOutput $htmlOutput;
    private array $tmpNormalizedImages = [];
    private const FONT_UNICODE = 'zapfdingbats';
    private const FONT_UNICODE_CHAR_SCISSORS = '"';
    private const FONT_UNICODE_CHAR_DOWN_ARROW = 't';

    public function __construct(QrBill $qrBill, string $language)
    {
        parent::__construct($qrBill, $language);
        $this->htmlOutput = new HtmlOutput($qrBill, $language);
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

        // in PHP < 8.3, images needs to be written to disk
        if (version_compare(PHP_VERSION, '8.3.0', '<')) {
            register_shutdown_function([$this, 'cleanupNormalizedImages']);

            $html = preg_replace_callback(
                '/src="data:(image\/png|image\/svg\+xml);base64,([^"]+)"/',
                fn($m) => $this->normalizeImageSrc($m[1], $m[2]),
                $html
            );
        }

        // replace base HTML special chars with the Dompdf-compatible ones
        $mapping = [
            '\\2702' => self::FONT_UNICODE_CHAR_SCISSORS,
            '\\25BC' => self::FONT_UNICODE_CHAR_DOWN_ARROW,
            '&#9986;' => self::FONT_UNICODE_CHAR_SCISSORS
        ];
        $html = str_replace(array_keys($mapping), array_values($mapping), $html);

        return $html;
    }

    private function normalizeImageSrc(string $mediaType, string $base64): string
    {
        $ext = $mediaType === 'image/svg+xml' ? 'svg' : 'png';
        $tmpFile = tempnam(sys_get_temp_dir(), 'php_swiss_qr_bill_dompdf_') . '.' . $ext;
        file_put_contents($tmpFile, base64_decode($base64));

        $this->tmpNormalizedImages[] = $tmpFile;

        return 'src="' . $tmpFile. '"';
    }

    private function cleanupNormalizedImages(): void
    {
        foreach ($this->tmpNormalizedImages as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $this->tmpNormalizedImages = [];
    }

    private function getTemplate(): string
    {
        $options = $this->getDisplayOptions();

        $font = self::FONT_UNICODE;
        $scissorsLeft = $options->isPositionScissorsAtBottom() ? '2.6mm' : '-0.9mm';

        return <<<EOT
<style type="text/css">
    html {
        margin: 0;
    }
    #qr-bill {
        font-family: Arial, Frutiger, Helvetica, "Liberation Sans"  !important;
    }
    #qr-bill-separate-info:before,
    #qr-bill-separate-info-text:before,
    #qr-bill-separate-info-text:after,
    #qr-bill #qr-bill-scissors {
        font-family: $font !important;
    }
    #qr-bill-separate-info-text:before {
        margin-right: -6mm;
    }
    #qr-bill-separate-info-text:before,
    #qr-bill-separate-info-text:after {
        letter-spacing: 0.7mm;
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
