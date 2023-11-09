<?php declare(strict_types=1);

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
    public function testQrFile(string $file, string $hash): void
    {
        $this->assertSame(
            $hash,
            hash_file('md5', $file)
        );
    }

    public function qrFileProvider(): array
    {
        return [
            [__DIR__ . '/qr-additional-information.png', '5089db74d380d6ece97d02c86cb35e2d'],
            [__DIR__ . '/qr-alternative-schemes.png', '0ac1061daa0114ba49708a5593471da0'],
            [__DIR__ . '/qr-full-set.png', 'b52be79babcc58485ee68fb4f722657c'],
            [__DIR__ . '/qr-international-ultimate-debtor.png', 'c56676e8c98f3ba54fac959c450a0995'],
            [__DIR__ . '/qr-minimal-setup.png', '72911d0c7d23298aeb14e4960204d6e0'],
            [__DIR__ . '/qr-payment-information-with-mediumlong-creditor-and-unknown-debtor.png', 'c347c35996eee781942ee2fa35da0a88'],
            [__DIR__ . '/qr-payment-information-without-amount-and-long-addresses.png', 'c5d23d3fe94aeed310bdf3b9349ce2f9'],
            [__DIR__ . '/qr-payment-information-without-amount.png', '1da10251b49d72aa48c35207f54d4f1e'],
            [__DIR__ . '/qr-payment-information-without-amount-but-debtor.png', '7f0276efa720448229a0cbeccc2aa805'],
            [__DIR__ . '/qr-payment-information-zero-amount.png', 'd779c23775b755e7d193b22e93b51ed4'],
            [__DIR__ . '/qr-payment-reference-non.png', '176a87b6743ebb3b4d15e9d85937780a'],
            [__DIR__ . '/qr-payment-reference-scor.png', '7abd60316b137fa472165faff8e4a28c'],
            [__DIR__ . '/qr-ultimate-debtor.png', 'ed279b73f429a8d9960b8dcb94c2c429'],

            [__DIR__ . '/proof-of-validation.png', 'a50bc5625703d22da79b46880ff3aef4'],
        ];
    }
}
