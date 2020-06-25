<?php

namespace Sprain\Tests\SwissQrBill\TestData\QrCodes;

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
            [__DIR__ . '/qr-additional-information.png', '837d81343c5f88e9848bdf2d693daccb'],
            [__DIR__ . '/qr-alternative-schemes.png', '72a10b79c4d6011abe784c556caf5e61'],
            [__DIR__ . '/qr-full-set.png', 'c0607307841dc5cbdf2c1c5499f869f7'],
            [__DIR__ . '/qr-minimal-setup.png', '86fb5a62d7a87d7e31ea91bb5c93bbf9'],
            [__DIR__ . '/qr-payment-information-without-amount.png', '0a22b900adc3f65e3aac674c1e891e2d'],
            [__DIR__ . '/qr-payment-information-without-amount-but-debtor.png', 'd3be7875b26b4c9f9f92434a947c5f5d'],
            [__DIR__ . '/qr-payment-information-zero-amount.png', 'b22b78c16c79dc84a4d890fb95e8d0c9'],
            [__DIR__ . '/qr-payment-reference-non.png', '92dd161c4cbecc1fdd00222d346f29d8'],
            [__DIR__ . '/qr-payment-reference-scor.png', '51113e54434835e0e628fbe9edc8f333'],
            [__DIR__ . '/qr-ultimate-debtor.png', '826e89dbbea4e48ce0b88fb708e7f990'],

            [__DIR__ . '/proof-of-validation.png', 'f539fb9c08472ac1f3d74598052e9e5b'],
        ];
    }
}