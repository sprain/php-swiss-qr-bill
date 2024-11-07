<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill\PaymentPart\Output\TcPdfOutput;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\PaymentPart\Output\DisplayOptions;
use Sprain\SwissQrBill\PaymentPart\Output\VerticalSeparatorSymbolPosition;
use Sprain\SwissQrBill\PaymentPart\Output\TcPdfOutput\TcPdfOutput;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\Tests\SwissQrBill\TestQrBillCreatorTrait;

final class TcPdfOutputTest extends TestCase
{
    use TestQrBillCreatorTrait;

    /**
     * @dataProvider validQrBillsProvider
     */
    public function testValidQrBills(string $name, QrBill $qrBill): void
    {
        $variations = [
            [
                'layout' => (new DisplayOptions())->setPrintable(false),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/TcPdfOutput/' . $name . '.svg.pdf'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(true),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/TcPdfOutput/' . $name . '.svg.print.pdf'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(false)->setDisplayScissors(true),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/TcPdfOutput/' . $name . '.svg.scissors.pdf'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(false)->setDisplayScissors(true)->setPositionScissorsAtBottom(true),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/TcPdfOutput/' . $name . '.svg.scissorsdown.pdf'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(false)->setDisplayTextDownArrows(true),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/TcPdfOutput/' . $name . '.svg.textarrows.pdf'
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

            $tcPdf = new \TCPDF('P', 'mm', 'A4', true, 'ISO-8859-1');
            $tcPdf->setPrintHeader(false);
            $tcPdf->setPrintFooter(false);
            $tcPdf->AddPage();

            $output = (new TcPdfOutput($qrBill, 'en', $tcPdf));
            $output
                ->setDisplayOptions($variation['layout'])
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

    public function testUtf8SpecialChars(): void
    {
        $file = __DIR__ . '/../../../TestData/TcPdfOutput/qr-utf8.svg.pdf';

        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr',
            'utf8SpecialCharsUltimateDebtor'
        ]);

        $tcPdf = new \TCPDF('P', 'mm', 'A4', true, 'ISO-8859-1');
        $tcPdf->setPrintHeader(false);
        $tcPdf->setPrintFooter(false);
        $tcPdf->AddPage();

        $output = (new TcPdfOutput($qrBill, 'en', $tcPdf));
        $output
            ->setDisplayOptions((new DisplayOptions())->setPrintable(true))
            ->setQrCodeImageFormat(QrCode::FILE_FORMAT_SVG)
            ->getPaymentPart();

        if ($this->regenerateReferenceFiles) {
            $tcPdf->Output($file, 'F');
        }

        $contents = $this->getActualPdfContents($tcPdf->Output($file, 'S'));

        $this->assertNotNull($contents);
        $this->assertSame($this->getActualPdfContents(file_get_contents($file)), $contents);
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
