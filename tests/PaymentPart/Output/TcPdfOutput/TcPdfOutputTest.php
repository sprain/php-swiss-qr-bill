<?php

namespace Sprain\Tests\SwissQrBill\PaymentPart\Output\HtmlOutput;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\HtmlOutput;
use Sprain\SwissQrBill\PaymentPart\Output\TcPdfOutput\TcPdfOutput;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\Tests\SwissQrBill\TestQrBillCreatorTrait;

class TcPdfOutputTest extends TestCase
{
    use TestQrBillCreatorTrait;

    private $regenerateReferenceTcPdfOutputs = false;

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

            if ($this->regenerateReferenceTcPdfOutputs) {
                $tcPdf->Output($file, 'F');
            }

            $this->assertSame(
                $this->cleanFileContents(file_get_contents($file)),
                $this->cleanFileContents($tcPdf->Output($file, 'S'))
            );
        }
    }

    private function cleanFileContents(string $fileContents): string
    {
        // Remove ids from files which are newly created in each pdf and make them non-comparable
        $pattern = '@<xmpMM:DocumentID>(.*)</xmpMM:DocumentID>@';
        $fileContents = preg_replace($pattern, '', $fileContents);

        $pattern = '@<xmpMM:InstanceID>(.*)</xmpMM:InstanceID>@';
        $fileContents = preg_replace($pattern, '', $fileContents);

        $pattern = '@<< /Size 12 /Root 11 0 R /Info 9 0 R /ID(.*)>>@';
        $fileContents = preg_replace($pattern, '', $fileContents);

        return $fileContents;
    }
}