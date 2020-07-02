<?php

namespace Sprain\Tests\SwissQrBill\PaymentPart\Output\FpdfOutput;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\PaymentPart\Output\FpdfOutput\FpdfOutput;
use Sprain\SwissQrBill\PaymentPart\Output\FpdfOutput\Template\QrBillFooter;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\Tests\SwissQrBill\TestQrBillCreatorTrait;

class FpdfOutputTest extends TestCase
{
    use TestQrBillCreatorTrait;

    /**
     * @dataProvider validQrBillsProvider
     * @param string $name
     * @param QrBill $qrBill
     */
    public function testValidQrBills(string $name, QrBill $qrBill)
    {
        $variations = [
            [
                'printable' => false,
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => dirname(dirname(dirname(__DIR__))) . '/TestData/FpdfOutput/' . $name . '.pdf'
            ],
            [
                'printable' => true,
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => dirname(dirname(dirname(__DIR__))) . '/TestData/FpdfOutput/' . $name . '.print.pdf'
            ]

            // Note: Testing the exact output with a png qr code is not possible, as the png contents are
            // not always exactly the same on each server configuration.
        ];

        foreach ($variations as $variation) {
            $file = $variation['file'];

            $fpdf = new QrBillFooter('P', 'mm', 'A4');
            $fpdf->AddPage();

            $output = new FpdfOutput($qrBill, 'en', $fpdf);
            $output
                ->setPrintable($variation['printable'])
                ->setQrCodeImageFormat($variation['format'])
                ->getPaymentPart();

            if (!file_exists($file)) {
                $this->regenerateReferenceFiles = true;
            }

            if ($this->regenerateReferenceFiles) {
                $fpdf->Output($file, 'F');
            }
            $contents = $this->getActualPdfContents($fpdf->Output($file, 'S'));

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
