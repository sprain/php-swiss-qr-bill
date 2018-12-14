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
            [__DIR__ . '/qr-alternative-schemes.png', 'f0a09d6e0b20c9bed5c78c6c66935467'],
            [__DIR__ . '/qr-full-set.png', '02dbb6e239bbcda8bcbf910d7d89f53f'],
            [__DIR__ . '/qr-minimal-setup.png', '6f93df1f942e8917d7272cefda1c389a'],
            [__DIR__ . '/qr-payment-information-without-amount-and-date.png', '251ce2297982770581f5da3f1b04e552'],
            [__DIR__ . '/qr-payment-reference-with-message.png', '6f93df1f942e8917d7272cefda1c389a'],
            [__DIR__ . '/qr-ultimate-creditor.png', 'ecdf7633464c5d0793bc8c24cf775920'],
            [__DIR__ . '/qr-ultimate-debtor.png', 'b036f39eba8e08c38f034f18404fc0ba'],
        ];
    }
}