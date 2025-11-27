<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill\PaymentPart\Output\DompdfOutput;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\PaymentPart\Output\DompdfOutput\DompdfOutput;
use Sprain\SwissQrBill\PaymentPart\Output\DisplayOptions;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\Tests\SwissQrBill\TestCompactSvgQrCodeTrait;
use Sprain\Tests\SwissQrBill\TraitValidQrBillsProvider;
use Dompdf\Dompdf;

final class DompdfOutputTest extends TestCase
{
    use TraitValidQrBillsProvider;
    use TestCompactSvgQrCodeTrait;

    #[DataProvider('validQrBillsProvider')]
    public function testValidQrBills(string $name, QrBill $qrBill)
    {
        $variations = [
            [
                'layout' => (new DisplayOptions())->setPrintable(false),
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => __DIR__ . '/../../../TestData/DompdfOutput/' . $name . $this->getCompact() . '.pdf'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(true),
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => __DIR__ . '/../../../TestData/DompdfOutput/' . $name . $this->getCompact() . '.print.pdf'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(false)->setDisplayScissors(true),
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => __DIR__ . '/../../../TestData/DompdfOutput/' . $name . $this->getCompact() . '.scissors.pdf'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(false)->setDisplayScissors(true)->setPositionScissorsAtBottom(true),
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => __DIR__ . '/../../../TestData/DompdfOutput/' . $name . $this->getCompact() . '.scissorsdown.pdf'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(false)->setDisplayTextDownArrows(true),
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => __DIR__ . '/../../../TestData/DompdfOutput/' . $name . $this->getCompact() . '.textarrows.pdf'
            ]
        ];

        foreach ($variations as $variation) {
            $file = $variation['file'];

            $dompdf = new Dompdf();
            $dompdf->setPaper('A4', 'portrait');

            $dompdfOutput = (new DompdfOutput($qrBill, 'en'));
            $html = $dompdfOutput
                ->setDisplayOptions($variation['layout'])
                ->getPaymentPart();

            $html = <<<EOT
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>$html</body>
</html>
EOT;

            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->render();

            $output = $dompdf->output();

            if ($this->regenerateReferenceFiles) {
                file_put_contents($file, $output);
            }

            $contents = $this->getActualPdfContents($output);

            $this->assertNotNull($contents);
            $this->assertSame($this->getActualPdfContents(file_get_contents($file)), $contents);
        }
    }

    private function getActualPdfContents(string $fileContents): ?string
    {
        // Extract actual pdf content and ignore all meta data which may differ in different versions of Fpdf
        $pattern = '/stream(.*?)endstream/s';
        preg_match($pattern, $fileContents, $matches);

        return $matches[1] ?? null;
    }
}
