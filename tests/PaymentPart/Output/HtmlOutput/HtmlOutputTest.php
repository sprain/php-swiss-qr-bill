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

    private $regenerateReferenceHtmlOutputs = false;

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
            ],
            [
                'printable' => false,
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => __DIR__ . '/../../../TestData/HtmlOutput/' . $name . '.png.html'
            ],
            [
                'printable' => true,
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => __DIR__ . '/../../../TestData/HtmlOutput/' . $name . '.print.png.html'
            ]
        ];

        foreach ($variations as $variation) {

            $file = $variation['file'];

            $htmlOutput = (new HtmlOutput($qrBill, 'en'));
            $htmlOutput->setPrintable($variation['printable']);
            $htmlOutput->setQrCodeImageFormat($variation['format']);
            $output = $htmlOutput->getPaymentPart();

            if ($this->regenerateReferenceHtmlOutputs) {
               file_put_contents($file, $output);
            }

            $this->assertSame(
                file_get_contents($file),
                $output
            );
        }
    }
}