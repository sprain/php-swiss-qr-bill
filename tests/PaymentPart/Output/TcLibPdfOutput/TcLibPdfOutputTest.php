<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill\PaymentPart\Output\TcLibPdfOutput;

use Com\Tecnick\Pdf\Tcpdf;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\Exception\InvalidTcLibPdfImageFormat;
use Sprain\SwissQrBill\PaymentPart\Output\DisplayOptions;
use Sprain\SwissQrBill\PaymentPart\Output\TcLibPdfOutput\TcLibPdfOutput;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\Tests\SwissQrBill\QrBillTestDataRepository;
use Sprain\Tests\SwissQrBill\TraitValidQrBillsProvider;

final class TcLibPdfOutputTest extends TestCase
{
    use TraitValidQrBillsProvider;

    private const TEST_DATA = __DIR__ . '/../../../TestData/TcLibPdfOutput/';

    #[DataProvider('validQrBillsProvider')]
    public function testValidQrBills(string $name, QrBill $qrBill): void
    {
        $variations = [
            [
                'layout' => (new DisplayOptions())->setPrintable(false),
                'file' => self::TEST_DATA . $name . '.txt'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(true),
                'file' => self::TEST_DATA . $name . '.print.txt'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(false)->setDisplayScissors(true),
                'file' => self::TEST_DATA . $name . '.scissors.txt'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(false)->setDisplayScissors(true)->setPositionScissorsAtBottom(true),
                'file' => self::TEST_DATA . $name . '.scissorsdown.txt'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(false)->setDisplayTextDownArrows(true),
                'file' => self::TEST_DATA . $name . '.textarrows.txt'
            ],
        ];

        foreach ($variations as $variation) {
            $tcLibPdf = $this->createPdf();

            $output = new TcLibPdfOutput($qrBill, 'en', $tcLibPdf);
            $output
                ->setDisplayOptions($variation['layout'])
                ->getPaymentPart();

            $this->assertPageContents($variation['file'], $tcLibPdf);
        }
    }

    public function testUtf8SpecialChars(): void
    {
        $qrBill = (new QrBillTestDataRepository())->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr',
            'utf8SpecialCharsUltimateDebtor'
        ]);

        $tcLibPdf = $this->createPdf();

        $output = new TcLibPdfOutput($qrBill, 'en', $tcLibPdf);
        $output
            ->setDisplayOptions((new DisplayOptions())->setPrintable(true))
            ->getPaymentPart();

        $this->assertPageContents(self::TEST_DATA . 'qr-utf8.print.txt', $tcLibPdf);
    }

    public function testOffsets(): void
    {
        $tcLibPdf = $this->createPdf();

        $output = new TcLibPdfOutput((new QrBillTestDataRepository())->getQrBillFullSet(), 'en', $tcLibPdf, 10, -100);
        $output
            ->setDisplayOptions((new DisplayOptions())->setPrintable(false)->setDisplayScissors(true))
            ->getPaymentPart();

        $this->assertPageContents(self::TEST_DATA . 'qr-full-set.offsets.txt', $tcLibPdf);
    }

    #[DataProvider('documentSetupProvider')]
    public function testOutputIsIndependentOfDocumentSetup(string $unit, array $page): void
    {
        $qrBill = (new QrBillTestDataRepository())->getQrBillFullSet();
        $layout = (new DisplayOptions())->setPrintable(false)->setDisplayScissors(true);

        $tcLibPdf = $this->createPdf($unit, $page);

        $output = new TcLibPdfOutput($qrBill, 'en', $tcLibPdf);
        $output
            ->setDisplayOptions($layout)
            ->getPaymentPart();

        $this->assertPageContents(self::TEST_DATA . 'qr-full-set.scissors.txt', $tcLibPdf);
    }

    public static function documentSetupProvider(): array
    {
        return [
            'points' => ['pt', ['format' => 'A4']],
            'inches' => ['in', ['format' => 'A4']],
            'page with margins' => ['mm', [
                'format' => 'A4',
                'margin' => ['PT' => 20, 'PB' => 30, 'PL' => 20, 'PR' => 20, 'CT' => 20, 'CB' => 30],
            ]],
        ];
    }

    public function testCurrentFontIsRestored(): void
    {
        $tcLibPdf = $this->createPdf();
        $tcLibPdf->font->insert($tcLibPdf->pon, 'helvetica', 'B', 17);
        $font = $tcLibPdf->font->getCurrentFont();

        $output = new TcLibPdfOutput((new QrBillTestDataRepository())->getQrBillFullSet(), 'en', $tcLibPdf);
        $output->getPaymentPart();

        $this->assertSame($font, $tcLibPdf->font->getCurrentFont());
    }

    public function testItRejectsSvgQrCodes(): void
    {
        $output = new TcLibPdfOutput((new QrBillTestDataRepository())->getQrBillFullSet(), 'en', $this->createPdf());

        $this->assertSame(QrCode::FILE_FORMAT_PNG, $output->getQrCodeImageFormat());

        $this->expectException(InvalidTcLibPdfImageFormat::class);
        $output->setQrCodeImageFormat(QrCode::FILE_FORMAT_SVG);
    }

    private function createPdf(string $unit = 'mm', array $page = ['format' => 'A4']): Tcpdf
    {
        $fonts = realpath(self::TEST_DATA . 'fonts');

        $tcLibPdf = new Tcpdf($unit, true, false, false, '', null, ['allowedPaths' => [$fonts]]);
        $tcLibPdf->addPage($page);

        // tc-lib-pdf does not ship any fonts. They are loaded explicitly instead of using K_PATH_FONTS,
        // as this constant is used by TCPDF as well.
        $tcLibPdf->font->insert($tcLibPdf->pon, 'zapfdingbats', '', 10, null, null, $fonts . '/zapfdingbats.json');
        $tcLibPdf->font->insert($tcLibPdf->pon, 'helvetica', 'B', 10, null, null, $fonts . '/helveticab.json');
        $tcLibPdf->font->insert($tcLibPdf->pon, 'helvetica', '', 10, null, null, $fonts . '/helvetica.json');

        return $tcLibPdf;
    }

    /**
     * Compares the page to a reference file, which describes its contents as text.
     *
     * When regenerating the reference files, the pdf itself is saved next to each of them. These pdf files exist
     * for visual inspection only and are never compared: Their bytes differ between versions of tc-lib-pdf and
     * between environments, and they contain a creation date and a random file id. So expect all of them to show
     * up as changed after regenerating, even if the output looks the same.
     */
    private function assertPageContents(string $file, Tcpdf $tcLibPdf): void
    {
        $pdf = $tcLibPdf->getOutPDFString();
        $contents = $this->describePageContents($pdf);

        if ($this->regenerateReferenceFiles) {
            file_put_contents($file, $contents);
            file_put_contents(preg_replace('/\.txt$/', '.pdf', $file), $pdf);
        }

        $this->assertNotSame('', $contents);
        $this->assertStringEqualsFile($file, $contents);
    }

    /**
     * Describes what is drawn on the page, one drawing operation per line.
     *
     * The raw content stream is not compared, as it differs between versions of tc-lib-pdf, e.g. in redundant
     * operators and in the precision of numbers. The same applies to the bytes of png images.
     */
    private function describePageContents(string $pdf): string
    {
        preg_match('/stream\n(.*?)endstream/s', $pdf, $matches);

        $number = '(-?\d+(?:\.\d+)?)';
        $patterns = [
            'font' => "~^BT /F$number $number Tf ET$~",
            'text' => "~^BT .*?$number $number Td \((.*)\) Tj ET$~",
            'dash' => "~^\[([\d. ]*)\] $number d$~",
            'move' => "~^$number $number m$~",
            'line' => "~^$number $number l$~",
            'rect' => "~^$number $number $number $number re$~",
            'matrix' => "~^$number $number $number $number $number $number cm$~",
            'image' => "~^q $number $number $number $number $number $number cm /IMG\w+ Do Q$~",
        ];

        $description = [];
        foreach (explode("\n", $matches[1] ?? '') as $line) {
            foreach ($patterns as $name => $pattern) {
                if (!preg_match($pattern, trim($line), $values)) {
                    continue;
                }

                $values = array_slice($values, 1);
                $text = $name === 'text' ? mb_convert_encoding((string) array_pop($values), 'UTF-8', 'Windows-1252') : null;
                $numbers = $this->formatNumbers(implode(' ', $values));

                if ($name === 'matrix' && $numbers === '1 0 0 1 0 0') {
                    break;
                }

                $description[] = trim($name . ' ' . $numbers . ' ' . $text);
                break;
            }
        }

        return implode("\n", $description) . "\n";
    }

    private function formatNumbers(string $numbers): string
    {
        return implode(' ', array_map(
            // Adding zero avoids negative zero
            fn (string $number): string => (string) (round((float) $number, 2) + 0.0),
            preg_split('/ +/', $numbers, -1, PREG_SPLIT_NO_EMPTY)
        ));
    }
}
