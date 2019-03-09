<?php

namespace Sprain\Tests\SwissQrBill\PaymentPart\Output\HtmlOutput;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\HtmlOutput;
use Sprain\SwissQrBill\QrBill;
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
                'file' => __DIR__ . '/../../../TestData/HtmlOutput/' . $name . '.html'
            ],
            [
                'printable' => true,
                'file' => __DIR__ . '/../../../TestData/HtmlOutput/' . $name . '.print.html'
            ]
        ];

        foreach ($variations as $variation) {

            $file = $variation['file'];

            $htmlOutput = (new HtmlOutput($qrBill, 'en'));
            $htmlOutput->setPrintable($variation['printable']);
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