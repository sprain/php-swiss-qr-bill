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
            [__DIR__ . '/qr-alternative-schemes.png', 'd27650f4a9697afaa5d0fa6d6149b0e7'],
            [__DIR__ . '/qr-full-set.png', 'f1b5ea9d6f4a96bec352e7f5bf58ab9b'],
            [__DIR__ . '/qr-minimal-setup.png', '72c0b722fd3eb7618a32dbf61aad7927'],
            [__DIR__ . '/qr-payment-information-without-amount-and-date.png', '9a3cbc1571bf302881f8f29abcd4f05f'],
            [__DIR__ . '/qr-payment-reference-with-message.png', '1f9427813214739d06f352eeec074f90'],
            [__DIR__ . '/qr-ultimate-creditor.png', '593cba9540aa64546cd43cbe2cfaac30'],
            [__DIR__ . '/qr-ultimate-debtor.png', '1da453cdde047505695f2a91c540a0c0'],
        ];
    }
}