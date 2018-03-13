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
            [__DIR__ . '/qr-alternative-schemes.png', '4600113fa99186059b159550c312abdb'],
            [__DIR__ . '/qr-full-set.png', '2453cd8ab49dfbc3d4da9b8081557f47'],
            [__DIR__ . '/qr-minimal-setup.png', 'c8ea1adfa1e22189c0b491073e0f9c7b'],
            [__DIR__ . '/qr-payment-reference-with-message.png', 'd17781636acb9de913c189e3ca78d0b0'],
            [__DIR__ . '/qr-ultimate-creditor.png', '6830f77bb6f80221891a3e9de3bcbcbd'],
            [__DIR__ . '/qr-ultimate-debtor.png', 'f05a37e51fa10c89a6e8da99a69848a0'],
        ];
    }
}