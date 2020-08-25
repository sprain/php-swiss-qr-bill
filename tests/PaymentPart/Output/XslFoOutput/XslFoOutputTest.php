<?php

namespace Sprain\Tests\SwissQrBill\PaymentPart\Output\XslFoOutput;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\PaymentPart\Output\XslFoOutput\XslFoOutput;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\Tests\SwissQrBill\TestQrBillCreatorTrait;

class XslFoOutputTest extends TestCase
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
                'file' => __DIR__ . '/../../../TestData/XslFoOutput/' . $name . '.svg.xml'
            ],
            [
                'printable' => true,
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/XslFoOutput/' . $name . '.svg.print.xml'
            ],
            [
                'printable' => false,
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => __DIR__ . '/../../../TestData/XslFoOutput/' . $name . '.png.xml'
            ],
            [
                'printable' => true,
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => __DIR__ . '/../../../TestData/XslFoOutput/' . $name . '.png.print.xml'
            ]
        ];

        foreach ($variations as $variation) {
            $file = $variation['file'];

            $xslFoOutput = (new XslFoOutput($qrBill, 'en'));
            $output = $xslFoOutput
                ->setPrintable($variation['printable'])
                ->setQrCodeImageFormat($variation['format'])
                ->getPaymentPart();

            $this->assertNotEmpty($output);

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