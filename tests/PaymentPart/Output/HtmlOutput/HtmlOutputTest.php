<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill\PaymentPart\Output\HtmlOutput;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\HtmlOutput;
use Sprain\SwissQrBill\PaymentPart\Output\PrintOptions;
use Sprain\SwissQrBill\PaymentPart\Output\Fonts;
use Sprain\SwissQrBill\PaymentPart\Output\VerticalSeparatorSymbolPositions;
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
                'layout' => (new PrintOptions())->setPrintable(false),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/HtmlOutput/' . $name . $this->getCompact() . '.svg.html'
            ],
            [
                'layout' => (new PrintOptions())->setPrintable(true),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/HtmlOutput/' . $name . $this->getCompact() . '.svg.print.html'
            ],
            [
                'layout' => (new PrintOptions())->setPrintable(false)->setSeparatorSymbol(true),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/HtmlOutput/' . $name . $this->getCompact() . '.svg.scissors.html'
            ],
            [
                'layout' => (new PrintOptions())->setPrintable(false)->setSeparatorSymbol(true)->setVerticalSeparatorSymbolPosition(VerticalSeparatorSymbolPositions::BOTTOM),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/HtmlOutput/' . $name . '.svg.scissorsdown.html'
            ],
            [
                'layout' => (new PrintOptions())->setPrintable(false)->setText(true)->setTextDownArrows(true),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/HtmlOutput/' . $name . '.svg.textarrows.html'
            ],
            [
                'layout' => (new PrintOptions())->setPrintable(false)->setSeparatorSymbol(true)->setText(false),
                'format' => QrCode::FILE_FORMAT_SVG,
                'file' => __DIR__ . '/../../../TestData/HtmlOutput/' . $name . '.svg.textno.html'
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
                ->setPrintOptions($variation['layout'])
                ->setQrCodeImageFormat($variation['format'])
                ->getPaymentPart();

            if (true) {
                file_put_contents($file, $output);
            }

            $this->assertSame(
                file_get_contents($file),
                $output
            );
        }
    }
}
