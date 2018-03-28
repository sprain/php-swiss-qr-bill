<?php

namespace Sprain\SwissQrBill\Tests\Reference;

use PHPUnit\Framework\TestCase;

/**
 * The test qr code files have been validated by the offical tool in
 * https://qr-validation.iso-payments.ch/gp/qrrechnung/validation
 *
 * These tests make sure that the files are still the correct verified files. This is important because they
 * will be used as reference files in other tests.
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
            [__DIR__ . '/qr-alternative-schemes.png', '9c1de9751e3be7f82dc4816f5e6c5c4f'],
            [__DIR__ . '/qr-full-set.png', '88cc582addea3b58a70851d99c77970e'],
            [__DIR__ . '/qr-minimal-setup.png', 'b2b7eb7ee9daeebe0589c46373a060b9'],
            [__DIR__ . '/qr-payment-reference-with-message.png', '3cf46d39d75332d12e40798b80d27d3e'],
            [__DIR__ . '/qr-ultimate-creditor.png', '52e06bde55994fb284aeaf764520d961'],
            [__DIR__ . '/qr-ultimate-debtor.png', '9237a7d00d5099a15cf93919819fd71e'],
        ];
    }
}