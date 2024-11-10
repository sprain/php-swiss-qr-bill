<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill\PaymentPart\Output\HtmlOutput;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\HtmlOutput;
use Sprain\SwissQrBill\PaymentPart\Output\DisplayOptions;
use Sprain\SwissQrBill\PaymentPart\Output\VerticalSeparatorSymbolPosition;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\Tests\SwissQrBill\TestCompactSvgQrCodeTrait;
use Sprain\Tests\SwissQrBill\TestQrBillCreatorTrait;

final class HtmlOutputTest extends TestCase
{
    use TestQrBillCreatorTrait;
    use TestCompactSvgQrCodeTrait;

    /**
     * @dataProvider validQrBillsProvider
     */
    public function testValidQrBills(string $name, QrBill $qrBill)
    {
        $variations = [
            [
                'layout' => (new DisplayOptions())->setPrintable(false),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/HtmlOutput/' . $name . $this->getCompact() . '.svg.html'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(true),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/HtmlOutput/' . $name . $this->getCompact() . '.svg.print.html'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(false)->setDisplayScissors(true),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/HtmlOutput/' . $name . $this->getCompact() . '.svg.scissors.html'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(false)->setDisplayScissors(true)->setPositionScissorsAtBottom(true),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/HtmlOutput/' . $name . $this->getCompact() . '.svg.scissorsdown.html'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(false)->setDisplayTextDownArrows(true),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/HtmlOutput/' . $name . $this->getCompact() . '.svg.textarrows.html'
            ],
            /* PNGs do not create the same output in all environments
            [
                'printable' => false,
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => __DIR__ . '/../../../TestData/HtmlOutput/' . $name . '.png.html'
            ],
            [
                'printable' => true,
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => __DIR__ . '/../../../TestData/HtmlOutput/' . $name . '.png.print.html'
            ]
            */
        ];

        foreach ($variations as $variation) {
            $file = $variation['file'];

            $htmlOutput = (new HtmlOutput($qrBill, 'en'));
            $output = $htmlOutput
                ->setDisplayOptions($variation['layout'])
                ->setQrCodeImageFormat($variation['format'])
                ->getPaymentPart();

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
