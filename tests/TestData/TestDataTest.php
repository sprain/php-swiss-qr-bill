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
            [__DIR__ . '/qr-additional-information.png', '00b350883b49687f643b654cb5f4ff99'],
            [__DIR__ . '/qr-alternative-schemes.png', '2308b5d51d85885559b55a6bfe972d36'],
            [__DIR__ . '/qr-full-set.png', '5eac83c30c2cc01321469f928649ea5f'],
            [__DIR__ . '/qr-minimal-setup.png', '1736bdbccd89fa16599bdd908da248b0'],
            [__DIR__ . '/qr-payment-information-without-amount.png', 'a95e41c5e808d498910fceefa40d6e3b'],
            [__DIR__ . '/qr-payment-reference-non.png', 'e999c59e2b83e8adba46d4eca3242ac1'],
            [__DIR__ . '/qr-payment-reference-scor.png', 'de185228c42c39b361a2a0f01627ec1f'],
            [__DIR__ . '/qr-ultimate-debtor.png', '9594b5397a24b5dfb96e2f58d198b8bf'],

            [__DIR__ . '/proof-of-validation.png', '145dadeadfea03e8132bd71761100fa9'],
        ];
    }
}