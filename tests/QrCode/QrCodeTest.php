<?php

namespace Sprain\SwissQrBill\Tests\QrCode;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\QrCode\QrCode;

class QrCodeTest extends TestCase
{
    /**
     * @dataProvider supportedExtensionsProvider
     */
    public function testSupportedFileExtensions($extension)
    {
        $qrCode = new QrCode('This is a test code');
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
     * @expectedException Sprain\SwissQrBill\QrCode\Exception\UnsupportedFileExtensionException
     */
    public function testUnsupportedFileExtensions($extension)
    {
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