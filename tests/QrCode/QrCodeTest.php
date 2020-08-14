<?php

namespace Sprain\Tests\SwissQrBill\QrCode;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\QrCode\Exception\UnsupportedFileExtensionException;
use Sprain\SwissQrBill\QrCode\QrCode;

class QrCodeTest extends TestCase
{
    /**
     * @dataProvider supportedExtensionsProvider
     */
    public function testSupportedFileExtensions($extension)
    {
        $qrCode = new QrCode('This is a test code');
        $qrCode->setLogoHeight(10);
        $qrCode->setLogoWidth(10);
        $testfile = __DIR__ . '/../TestData/testfile.' . $extension;

        if (!is_writable(dirname($testfile))) {
            $this->markTestSkipped();
            return;
        }

        $this->assertNull($qrCode->writeFile($testfile));
        unlink($testfile);
    }

    public function supportedExtensionsProvider()
    {
        return [
            ['svg'],
            ['png']
        ];
    }

    /**
     * @dataProvider unsupportedExtensionsProvider
     *
     */
    public function testUnsupportedFileExtensions($extension)
    {
        $this->expectException(UnsupportedFileExtensionException::class);

        $qrCode = new QrCode('This is a test code');
        $this->assertNull($qrCode->writeFile(__DIR__ . '/../TestData/testfile.' . $extension));
    }

    public function unsupportedExtensionsProvider()
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
