<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill\PaymentPart\Output\TcPdfOutput;

use PHPUnit\Framework\TestCase;
use setasign\Fpdi\Tcpdf\Fpdi;
use Sprain\SwissQrBill\PaymentPart\Output\PrintOptions;
use Sprain\SwissQrBill\PaymentPart\Output\Fonts;
use Sprain\SwissQrBill\PaymentPart\Output\VerticalSeparatorSymbolPositions;
use Sprain\SwissQrBill\PaymentPart\Output\TcPdfOutput\TcPdfOutput;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\Tests\SwissQrBill\TestQrBillCreatorTrait;

final class FpdiOutputTest extends TestCase
{
    use TestQrBillCreatorTrait;

    /**
     * @dataProvider validQrBillsProvider
     */
    public function testValidQrBills(string $name, QrBill $qrBill): void
    {
        $variations = [
            [
                'layout' => (new PrintOptions())->setPrintable(false),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/TcPdfOutput/' . $name . '.svg.pdf'
            ],
            [
                'layout' => (new PrintOptions())->setPrintable(true),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/TcPdfOutput/' . $name . '.svg.print.pdf'
            ],
            [
                'layout' => (new PrintOptions())->setPrintable(false)->setSeparatorSymbol(true),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/TcPdfOutput/' . $name . '.svg.scissors.pdf'
            ],
            [
                'layout' => (new PrintOptions())->setPrintable(false)->setSeparatorSymbol(true)->setVerticalSeparatorSymbolPosition(VerticalSeparatorSymbolPositions::BOTTOM),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/TcPdfOutput/' . $name . '.svg.scissorsdown.pdf'
            ],
            [
                'layout' => (new PrintOptions())->setPrintable(false)->setText(true)->setTextDownArrows(true),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/TcPdfOutput/' . $name . '.svg.textarrows.pdf'
            ],
            [
                'layout' => (new PrintOptions())->setPrintable(false)->setSeparatorSymbol(true)->setText(false),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/TcPdfOutput/' . $name . '.svg.textno.pdf'
            ],
            /* PNGs do not create the same output in all environments
            [
                'printable' => false,
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => __DIR__ . '/../../../TestData/TcPdfOutput/' . $name . '.png.pdf'
            ],
            [
                'printable' => true,
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => __DIR__ . '/../../../TestData/TcPdfOutput/' . $name . '.png.print.pdf'
            ]
            */
        ];

        foreach ($variations as $variation) {
            $file = $variation['file'];

            $tcPdf = new Fpdi('P', 'mm', 'A4', true, 'ISO-8859-1');
            $tcPdf->setPrintHeader(false);
            $tcPdf->setPrintFooter(false);
            $tcPdf->AddPage();

            $output = (new TcPdfOutput($qrBill, 'en', $tcPdf));
            $output
                ->setPrintOptions($variation['layout'])
                ->setQrCodeImageFormat($variation['format'])
                ->getPaymentPart();

            if ($this->regenerateReferenceFiles) {
                $tcPdf->Output($file, 'F');
            }

            $contents = $this->getActualPdfContents($tcPdf->Output($file, 'S'));

            $this->assertNotNull($contents);
            $this->assertSame($this->getActualPdfContents(file_get_contents($file)), $contents);
        }
    }

    private function getActualPdfContents(string $fileContents): ?string
    {
        // Extract actual pdf content and ignore all meta data which may differ in different versions of TcPdf
        $pattern = '/stream(.*?)endstream/s';
        preg_match($pattern, $fileContents, $matches);

        if (isset($matches[1])) {
            return $matches[1];
        }

        return null;
    }
}
