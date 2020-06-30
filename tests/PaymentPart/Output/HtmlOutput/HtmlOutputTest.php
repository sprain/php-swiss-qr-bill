<?php

namespace Sprain\Tests\SwissQrBill\PaymentPart\Output\HtmlOutput;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\HtmlOutput;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\Tests\SwissQrBill\TestQrBillCreatorTrait;

class HtmlOutputTest extends TestCase
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
                'file' => __DIR__ . '/../../../TestData/HtmlOutput/' . $name . '.html'
            ],
            [
                'printable' => true,
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/HtmlOutput/' . $name . '.print.html'
            ]

            // Note: Testing the exact output with a png qr code is not possible, as the png contents are
            // not always exactly the same on each server configuration.
        ];

        foreach ($variations as $variation) {

            $file = $variation['file'];

            $htmlOutput = (new HtmlOutput($qrBill, 'en'));
            $htmlOutput->setPrintable($variation['printable']);
            $htmlOutput->setQrCodeImageFormat($variation['format']);
            $output = $htmlOutput->getPaymentPart();

            if ($this->regenerateReferenceFiles) {
               file_put_contents($file, $output);
            }

            $this->assertSame(
                file_get_contents($file),
                $output
            );
        }
    }
}