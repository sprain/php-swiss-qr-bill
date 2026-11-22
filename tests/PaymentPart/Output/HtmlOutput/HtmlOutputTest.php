<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill\PaymentPart\Output\HtmlOutput;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\HtmlOutput;
use Sprain\SwissQrBill\PaymentPart\Output\DisplayOptions;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\Tests\SwissQrBill\TestCompactSvgQrCodeTrait;
use Sprain\Tests\SwissQrBill\TraitValidQrBillsProvider;

final class HtmlOutputTest extends TestCase
{
    use TraitValidQrBillsProvider;
    use TestCompactSvgQrCodeTrait;

    #[DataProvider('validQrBillsProvider')]
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

    public static function displayOptionsProvider(): array
    {
        return [
            'default' => [(new DisplayOptions())],
            'printable' => [(new DisplayOptions())->setPrintable(true)],
            'scissors' => [(new DisplayOptions())->setDisplayScissors(true)],
            'scissors at bottom' => [(new DisplayOptions())->setDisplayScissors(true)->setPositionScissorsAtBottom(true)],
            'text down arrows' => [(new DisplayOptions())->setDisplayTextDownArrows(true)],
        ];
    }

    /**
     * The styles of the payment part must not affect the surrounding document.
     */
    #[DataProvider('displayOptionsProvider')]
    public function testStylesAreScopedToPaymentPart(DisplayOptions $displayOptions): void
    {
        $output = (new HtmlOutput($this->createQrBill(), 'en'))
            ->setDisplayOptions($displayOptions)
            ->getPaymentPart();

        $this->assertSame(1, preg_match_all('/<style>(.*?)<\/style>/s', $output, $styleBlocks));
        $css = preg_replace('/\/\*.*?\*\//s', '', $styleBlocks[1][0]);

        $this->assertGreaterThan(0, preg_match_all('/([^{}]+)\{[^{}]*\}/', $css, $rules));
        foreach ($rules[1] as $selectorList) {
            // Split at commas, but not within parentheses, e.g. in :where(tr, td)
            foreach (preg_split('/,(?![^(]*\))/', $selectorList) as $selector) {
                $this->assertMatchesRegularExpression(
                    '/^#qr-bill(?![\w-])|^#qr-bill-[\w-]+/',
                    trim($selector),
                    'Selector is not scoped to the payment part.'
                );
            }
        }
    }

    /**
     * The payment part resets the styles it relies on, before its own rules are applied.
     */
    public function testStylesAreResetBeforeOwnRules(): void
    {
        $output = (new HtmlOutput($this->createQrBill(), 'en'))->getPaymentPart();

        preg_match('/<style>(.*?)<\/style>/s', $output, $styleBlock);
        $css = preg_replace('/\/\*.*?\*\//s', '', $styleBlock[1]);
        preg_match('/([^{}]+)\{([^{}]*)\}/', $css, $firstRule);

        $this->assertSame('#qr-bill *', trim($firstRule[1]));
        foreach (['margin', 'padding', 'border', 'float', 'position', 'font-size', 'font-weight', 'line-height', 'letter-spacing', 'text-align', 'color'] as $property) {
            $this->assertMatchesRegularExpression('/(^|;)\s*' . $property . '\s*:/', $firstRule[2], $property . ' is not reset.');
        }
    }

    private function createQrBill(): QrBill
    {
        return self::validQrBillsProvider()[0][1];
    }
}
