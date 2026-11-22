<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill\PaymentPart\Output\FpdfOutput;

use PHPUnit\Framework\Attributes\DataProvider;
use Fpdf\Fpdf;
use Sprain\Tests\SwissQrBill\QrBillTestDataRepository;
use Fpdf\Traits\MemoryImageSupport\MemImageTrait;
use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\Exception\InvalidFpdfImageFormat;
use Sprain\SwissQrBill\PaymentPart\Output\FpdfOutput\FpdfOutput;
use Sprain\SwissQrBill\PaymentPart\Output\FpdfOutput\UnsupportedEnvironmentException;
use Sprain\SwissQrBill\PaymentPart\Output\DisplayOptions;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\Tests\SwissQrBill\TraitValidQrBillsProvider;
use Sprain\SwissQrBill\PaymentPart\Output\FpdfOutput\FpdfTrait;
use Sprain\SwissQrBill\PaymentPart\Output\FpdfOutput\MissingTraitException;

final class FpdfOutputTest extends TestCase
{
    use TraitValidQrBillsProvider;

    #[DataProvider('validQrBillsProvider')]
    public function testValidQrBills(string $name, QrBill $qrBill): void
    {
        $variations = [
            [
                'layout' => (new DisplayOptions())->setPrintable(false),
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => dirname(__DIR__, 3) . '/TestData/FpdfOutput/' . $name . '.pdf'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(true),
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => dirname(__DIR__, 3) . '/TestData/FpdfOutput/' . $name . '.print.pdf'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(false)->setDisplayScissors(true),
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => dirname(__DIR__, 3) . '/TestData/FpdfOutput/' . $name . '.scissors.pdf'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(false)->setDisplayScissors(true)->setPositionScissorsAtBottom(true),
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => __DIR__ . '/../../../TestData/FpdfOutput/' . $name . '.svg.scissorsdown.pdf'
            ],
            [
                'layout' => (new DisplayOptions())->setPrintable(false)->setDisplayTextDownArrows(true),
                'format' => QrCode::FILE_FORMAT_PNG,
                'file' => __DIR__ . '/../../../TestData/FpdfOutput/' . $name . '.svg.textarrows.pdf'
            ]
        ];

        foreach ($variations as $variation) {
            $file = $variation['file'];

            $fpdf = $this->instantiateFpdf();
            $fpdf->AddPage();

            $output = new FpdfOutput($qrBill, 'en', $fpdf);


            try {
            $output
                ->setDisplayOptions($variation['layout'])
                ->setQrCodeImageFormat($variation['format'])
                ->getPaymentPart();

            } catch (\Exception) {
                foreach ($qrBill->getViolations() as $violation) {
                    print $violation->getMessage()."\n";
                }
                exit;
            }

            if ($this->regenerateReferenceFiles) {
                $fpdf->Output($file, 'F');
            }

            $contents = $this->getActualPdfContents($fpdf->Output($file, 'S'));

            $this->assertNotNull($contents);
            $this->assertSame($this->getActualPdfContents(file_get_contents($file)), $contents);
        }
    }

    public function testItThrowsMissingTraitException(): void
    {
        $this->expectException(MissingTraitException::class);

        $qrBill = (new QrBillTestDataRepository())->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr'
        ]);

        $fpdf = $this->instantiateFpdf(null, false);
        $fpdf->AddPage();

        $output = new FpdfOutput($qrBill, 'en', $fpdf);
        $output
            ->setDisplayOptions((new DisplayOptions())->setPrintable(false)->setDisplayScissors(true))
            ->setQrCodeImageFormat(QrCode::FILE_FORMAT_PNG)
            ->getPaymentPart();
    }

    public function testUtf8SpecialChars(): void
    {
        $file = __DIR__ . '/../../../TestData/FpdfOutput/qr-utf8.svg.pdf';

        $qrBill = (new QrBillTestDataRepository())->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr',
            'utf8SpecialCharsUltimateDebtor'
        ]);

        $fpdf = $this->instantiateFpdf();
        $fpdf->AddPage();

        $output = new FpdfOutput($qrBill, 'en', $fpdf);
        $output
            ->setDisplayOptions((new DisplayOptions())->setPrintable(true))
            ->setQrCodeImageFormat(QrCode::FILE_FORMAT_PNG)
            ->getPaymentPart();

        if ($this->regenerateReferenceFiles) {
            $fpdf->Output($file, 'F', true);
        }

        $contents = $this->getActualPdfContents($fpdf->Output($file, 'S', true));

        $this->assertNotNull($contents);
        $this->assertSame($this->getActualPdfContents(file_get_contents($file)), $contents);
    }

    public function testItThrowsSvgNotSupportedException(): void
    {
        $this->expectException(InvalidFpdfImageFormat::class);

        $qrBill = (new QrBillTestDataRepository())->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr'
        ]);

        $fpdf = $this->instantiateFpdf();
        $fpdf->AddPage();

        $output = new FpdfOutput($qrBill, 'en', $fpdf);
        $output
            ->setQrCodeImageFormat(QrCode::FILE_FORMAT_SVG)
            ->getPaymentPart();
    }

    public function testItThrowsUnsupportedEnvironmentException(): void
    {
        if (ini_get('allow_url_fopen')) {
            $this->markTestSkipped("This test only works in hardened environment.");
        }

        $this->expectException(UnsupportedEnvironmentException::class);

        $qrBill = (new QrBillTestDataRepository())->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr'
        ]);

        $fpdf = $this->instantiateFpdf(false);
        $fpdf->AddPage();

        $output = new FpdfOutput($qrBill, 'en', $fpdf);
        $output
            ->setQrCodeImageFormat(QrCode::FILE_FORMAT_PNG)
            ->getPaymentPart();
    }

    private function getActualPdfContents(string $fileContents): ?string
    {
        // Extract actual pdf content and ignore all meta data which may differ in different versions of Fpdf
        $pattern = '/stream(.*?)endstream/s';
        preg_match($pattern, $fileContents, $matches);
        return $matches[1] ?? null;
    }

    public function testOutputIsIndependentOfDocumentState(): void
    {
        $qrBill = (new QrBillTestDataRepository())->getQrBillFullSet();
        $layout = (new DisplayOptions())->setPrintable(false)->setDisplayScissors(true);

        $defaultFpdf = $this->instantiateFpdf();
        $defaultFpdf->AddPage();
        $defaultFpdf->SetFont('Courier', 'IU', 20);
        $expectedContent = $this->getAddedPageContent($defaultFpdf, new FpdfOutput($qrBill, 'en', $defaultFpdf), $layout);

        $fpdf = $this->instantiateFpdf();
        $fpdf->AddPage();
        $fpdf->SetFont('Courier', 'IU', 20);
        $fpdf->SetTextColor(255, 0, 0);
        $fpdf->SetDrawColor(0, 0, 255);
        $fpdf->SetFillColor(0, 255, 0);
        $fpdf->SetLineWidth(1.5);
        $fpdf->SetAutoPageBreak(true, 30);
        $fpdf->swissQrBillSetDash(2, 1);
        $stateBefore = $this->getFpdfState($fpdf);

        $content = $this->getAddedPageContent($fpdf, new FpdfOutput($qrBill, 'en', $fpdf), $layout);

        $this->assertSame($expectedContent, $content);
        $this->assertSame($stateBefore, $this->getFpdfState($fpdf));
    }

    /**
     * Returns the content which the output added to the current page.
     */
    private function getAddedPageContent(Fpdf $fpdf, FpdfOutput $output, DisplayOptions $layout): string
    {
        $getPage = \Closure::bind(fn (): string => $this->pages[$this->page], $fpdf, $fpdf::class);
        $before = strlen($getPage());

        $output->setDisplayOptions($layout)->getPaymentPart();

        return substr($getPage(), $before);
    }

    /**
     * @return array<string, mixed>
     */
    private function getFpdfState(Fpdf $fpdf): array
    {
        return \Closure::bind(fn (): array => [
            'font' => [$this->FontFamily, $this->FontStyle, $this->FontSizePt, $this->FontSize, $this->underline, $this->CurrentFont],
            // Fonts used by the payment part are added, but the existing fonts must stay intact
            'fonts' => array_intersect_key($this->fonts, ['courierIU' => true, 'courierI' => true]),
            'lineWidth' => $this->LineWidth,
            'colors' => [$this->DrawColor, $this->FillColor, $this->TextColor, $this->ColorFlag],
            'cellMargin' => $this->cMargin,
            'pageBreak' => [$this->AutoPageBreak, $this->bMargin, $this->PageBreakTrigger],
        ], $fpdf, $fpdf::class)();
    }

    private function instantiateFpdf($withMemImageSupport = null, $withBundledTrait = null): Fpdf
    {
        if ($withMemImageSupport === null) {
            $withMemImageSupport = !ini_get('allow_url_fopen');
        }

        if ($withBundledTrait === null) {
            $withBundledTrait = true;
        }

        if ($withMemImageSupport) {
            if ($withBundledTrait) {
                return new class('P', 'mm', 'A4') extends Fpdf {
                    use MemImageTrait;
                    use FpdfTrait;
                };
            }
            return new class('P', 'mm', 'A4') extends Fpdf {
                use MemImageTrait;
            };
        }

        if ($withBundledTrait) {
            return new class('P', 'mm', 'A4') extends Fpdf {
                use FpdfTrait;
            };
        }

        return new Fpdf('P', 'mm', 'A4');
    }
}
