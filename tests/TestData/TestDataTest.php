<?php

namespace Sprain\SwissQrBill\Tests\Reference;

use PHPUnit\Framework\TestCase;

/**
 * These tests make sure that the qr code examples are the unchanged reference files to be used in other tests.
 */
class TestDataTest extends TestCase
{
    /**
     * @dataProvider qrFileProvider
     */
    public function testQrFile($file, $hash)
    {
        $this->assertSame(
            $hash,
            hash_file('md5', $file)
        );
    }

    public function qrFileProvider()
    {
        return [
            [__DIR__ . '/qr-alternative-schemes.png', '9422d6b2d7536f807e1d94f8deb3cc0e'],
            [__DIR__ . '/qr-full-set.png', 'b15f304ebf0bd6313d245d5f9730cb35'],
            [__DIR__ . '/qr-minimal-setup.png', 'bf2e7e5861bf6e4c6c365556eabee805'],
            [__DIR__ . '/qr-payment-information-without-amount.png', 'd241fabe7028f3fa007e05762ceb6735'],
            [__DIR__ . '/qr-payment-reference-with-message.png', 'bf2e7e5861bf6e4c6c365556eabee805'],
            [__DIR__ . '/qr-ultimate-creditor.png', '09be6477de11b9caabae1d8944e75c30'],
            [__DIR__ . '/qr-ultimate-debtor.png', '9a2ee7a3d5c914cbab7f021c50676f71'],
        ];
    }
}