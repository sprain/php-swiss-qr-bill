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
            [__DIR__ . '/qr-additional-information.png', 'fb9c45f2bc0bf92e941abc64ba50bebb'],
            [__DIR__ . '/qr-alternative-schemes.png', '138ac88c4f5d9d127fb11326daa9292a'],
            [__DIR__ . '/qr-full-set.png', '969b870f49362a611f62235190fcf03a'],
            [__DIR__ . '/qr-minimal-setup.png', '8ed64ec5a2c7a02b1e07aa5eb8e3a7a0'],
            [__DIR__ . '/qr-payment-information-without-amount.png', '831b0edb4aca22f5df5f8dec530461dc'],
            [__DIR__ . '/qr-payment-information-without-amount-but-debtor.png', '9d4db076385276242cc4a362ee29b58d'],
            [__DIR__ . '/qr-payment-information-zero-amount.png', '1e35e396920f07fd0933ecdaabe910f9'],
            [__DIR__ . '/qr-payment-reference-non.png', 'abb3ace7ea9f3ef848a335eba98a2375'],
            [__DIR__ . '/qr-payment-reference-scor.png', '7e3389311c54ff27e1537d1083656a07'],
            [__DIR__ . '/qr-ultimate-debtor.png', 'ac5d8f4155c1d10460de5a0aaea35767'],

            [__DIR__ . '/proof-of-validation.png', '6bf37bad5b10f54971068a90de36fd0e'],
        ];
    }
}