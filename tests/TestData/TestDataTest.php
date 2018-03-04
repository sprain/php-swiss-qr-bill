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
            [__DIR__ . '/qr-minimal-setup.png', '79a08f144bd1ef9c884be00068cc534d'],
            [__DIR__ . '/qr-ultimate-creditor.png', 'ffc358811c92c65b3b744c07ec0785cb'],
            [__DIR__ . '/qr-ultimate-debtor.png', 'af0bc2f9bacf16d3b81423817fc2bf43'],
        ];
    }
}