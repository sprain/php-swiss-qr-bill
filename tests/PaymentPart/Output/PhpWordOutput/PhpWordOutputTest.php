<?php

namespace Sprain\Tests\SwissQrBill\PaymentPart\Output\PhpWordOutput;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\Exception\InvalidPhpWordImageFormat;
use Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\PhpWordOutput;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\Tests\SwissQrBill\TestQrBillCreatorTrait;

class PhpWordOutputTest extends TestCase
{
    use TestQrBillCreatorTrait;

    /**
     * @dataProvider validQrBillsProvider
     */
    public function testValidQrBills(string $name, QrBill $qrBill): void
    {
        $variations = [
                [
                        'printable' => false,
                        'format' => QrCode::FILE_FORMAT_PNG,
                        'file' => dirname(__DIR__, 3) . '/TestData/PhpWordOutput/' . $name . '.docx'
                ],
                [
                        'printable' => true,
                        'format' => QrCode::FILE_FORMAT_PNG,
                        'file' => dirname(__DIR__, 3) . '/TestData/PhpWordOutput/' . $name . '.print.docx'
                ]
        ];

        foreach ($variations as $variation) {
            $file = $variation['file'];

            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $phpWord->getDocInfo()->setCreated(strtotime('2022-05-18 00:00:00'));
            $phpWord->getDocInfo()->setModified(strtotime('2022-05-18 00:00:00'));
            $phpWord->addSection();

            $output = new PhpWordOutput($qrBill, 'en', $phpWord);
            $output
                    ->setPrintable($variation['printable'])
                    ->setQrCodeImageFormat($variation['format'])
                    ->getPaymentPart();

            if ($this->regenerateReferenceFiles) {
                $phpWord->save($file);
            }

            $tmpFile = tempnam(sys_get_temp_dir(), 'phpWordOutput');
            if (false === $tmpFile) {
                throw new \RuntimeException('Could not create temporary file');
            }
            $phpWord->save($tmpFile);

            $zipArchive = new \ZipArchive();
            $this->assertTrue($zipArchive->open($file));

            $tmpZipArchive = new \ZipArchive();
            $this->assertTrue($tmpZipArchive->open($tmpFile));

            $count = $zipArchive->count();
            $this->assertCount($count, $tmpZipArchive);

            for ($i = 0; $i < $count; $i++) {
                $name = $zipArchive->getNameIndex($i);
                $content = $zipArchive->getFromName($name);
                $tmpContent = $tmpZipArchive->getFromName($name);
                $this->assertSame(
                    chunk_split($content, 150),
                    chunk_split($tmpContent, 150),
                );
            }
        }
    }

    public function testItThrowsSvgNotSupportedException(): void
    {
        $this->expectException(InvalidPhpWordImageFormat::class);

        $qrBill = $this->createQrBill([
                'header',
                'creditorInformationQrIban',
                'creditor',
                'paymentAmountInformation',
                'paymentReferenceQr'
        ]);

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $phpWord->addSection();

        $output = new PhpWordOutput($qrBill, 'en', $phpWord);
        $output
                ->setQrCodeImageFormat(QrCode::FILE_FORMAT_SVG)
                ->getPaymentPart();
    }
}
