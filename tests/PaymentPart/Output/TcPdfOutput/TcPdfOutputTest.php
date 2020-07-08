<?php

namespace Sprain\Tests\SwissQrBill\PaymentPart\Output\HtmlOutput;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\PaymentPart\Output\TcPdfOutput\TcPdfOutput;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\Tests\SwissQrBill\TestQrBillCreatorTrait;

class TcPdfOutputTest extends TestCase
{
    use TestQrBillCreatorTrait;

    /**
     * @dataProvider validQrBillsProvider
     */
    public function testValidQrBills(string $name, QrBill $qrBill)
    {
        $variations = [
            [
                'printable' => false,
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/TcPdfOutput/' . $name . '.pdf'
            ],
            [
                'printable' => true,
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/TcPdfOutput/' . $name . '.print.pdf'
            ]

            // Note: Testing the exact output with a png qr code is not possible, as the png contents are
            // not always exactly the same on each server configuration.
        ];

        foreach ($variations as $variation) {
            $file = $variation['file'];

            $tcPdf = new \TCPDF('P', 'mm', 'A4', true, 'ISO-8859-1');
            $tcPdf->setPrintHeader(false);
            $tcPdf->setPrintFooter(false);
            $tcPdf->AddPage();

            $tcPdf->setDocCreationTimestamp(strtotime('2020-06-30 00:00'));
            $tcPdf->setDocModificationTimestamp(strtotime('2020-06-30 00:00'));

            $tcPdfOutput = (new TcPdfOutput($qrBill, 'en', $tcPdf));
            $tcPdfOutput->setPrintable($variation['printable']);
            $tcPdfOutput->setQrCodeImageFormat($variation['format']);
            $tcPdfOutput->getPaymentPart();

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
