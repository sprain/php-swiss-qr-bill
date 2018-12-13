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
            [__DIR__ . '/qr-alternative-schemes.png', 'cfb26bbcb91b71183b4e5dc732af27b8'],
            [__DIR__ . '/qr-full-set.png', '1f6f422ee63f12e3af8842b7c621ece2'],
            [__DIR__ . '/qr-minimal-setup.png', '083ef8df028070c379bbcfe8174a5693'],
            [__DIR__ . '/qr-payment-information-without-amount-and-date.png', '133d218830c3af7d80f571fa9e4458ab'],
            [__DIR__ . '/qr-payment-reference-with-message.png', '0a4b8d1dc8046e7d651814e948e9878d'],
            [__DIR__ . '/qr-ultimate-creditor.png', 'f57a616a0ef87cd577fcf5946f1a45be'],
            [__DIR__ . '/qr-ultimate-debtor.png', 'fdbaee68d5864894797b6c42bcc9d914'],
        ];
    }
}