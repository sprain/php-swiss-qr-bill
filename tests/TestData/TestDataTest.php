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
            [__DIR__ . '/qr-additional-information.png', '5e6b1246239a91b9a2971916284048c7'],
            [__DIR__ . '/qr-alternative-schemes.png', '2794b92f0e8c2bd292cff604a6fe2b96'],
            [__DIR__ . '/qr-full-set.png', '65461878a3a797567c9a2640b6a784e2'],
            [__DIR__ . '/qr-minimal-setup.png', '00d82b2797622e24b4736b1974094c75'],
            [__DIR__ . '/qr-payment-information-without-amount.png', '82abf9e7e2efb63d2b8d1d3809b82ddd'],
            [__DIR__ . '/qr-ultimate-debtor.png', '835efdaebe41b0b30e3f967b7aa6a0cb'],

            [__DIR__ . '/proof-of-validation.png', '726bd835f52cb98d3874309222f7a727'],
        ];
    }
}