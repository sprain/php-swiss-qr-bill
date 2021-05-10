<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill\QrCode;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\QrCode\Exception\UnsupportedFileExtensionException;
use Sprain\SwissQrBill\QrCode\QrCode;

final class QrCodeTest extends TestCase
{
    /**
     * @dataProvider supportedExtensionsProvider
     */
    public function testSupportedFileExtensions(string $extension): void
    {
        $qrCode = new QrCode('This is a test code');
        $qrCode->setLogoHeight(10);
        $qrCode->setLogoWidth(10);
        $testfile = __DIR__ . '/../TestData/testfile.' . $extension;

        if (!is_writable(dirname($testfile))) {
            $this->markTestSkipped();
            return;
        }

        $qrCode->writeFile($testfile);
        $this->assertTrue(file_exists($testfile));
        unlink($testfile);
    }

    public function supportedExtensionsProvider(): array
    {
        return [
            ['svg'],
            ['png']
        ];
    }

    /**
     * @dataProvider unsupportedExtensionsProvider
     */
    public function testUnsupportedFileExtensions(?string $extension): void
    {
        $this->expectException(UnsupportedFileExtensionException::class);

        $qrCode = new QrCode('This is a test code');
        $qrCode->writeFile(__DIR__ . '/../TestData/testfile.' . $extension);
    }

    public function unsupportedExtensionsProvider(): array
    {
        return [
            ['eps'],
            ['jpg'],
            ['gif'],
            [''],
            [null]
        ];
    }
}
