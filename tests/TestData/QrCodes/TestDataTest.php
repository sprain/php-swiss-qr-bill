<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill\TestData\QrCodes;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * These tests make sure that the qr code examples are the unchanged reference files to be used in other tests.
 */
class TestDataTest extends TestCase
{
    #[DataProvider('qrFileProvider')]
    public function testQrFile(string $file, string $hash): void
    {
        $this->assertSame(
            $hash,
            hash_file('md5', $file)
        );
    }

    public static function qrFileProvider(): array
    {
        return [
            [__DIR__ . '/qr-additional-information.png', '5089db74d380d6ece97d02c86cb35e2d'],
            [__DIR__ . '/qr-alternative-schemes.png', '0ac1061daa0114ba49708a5593471da0'],
            [__DIR__ . '/qr-full-set.png', '9bedd6bdfa1819a80b7f27bcd2720989'],
            [__DIR__ . '/qr-international-ultimate-debtor.png', 'c52014c1b12faa2d6071406c43f300d7'],
            [__DIR__ . '/qr-minimal-setup.png', '72911d0c7d23298aeb14e4960204d6e0'],
            [__DIR__ . '/qr-payment-information-with-mediumlong-creditor-and-unknown-debtor.png', '85c0267c6b0bbe427f7f3568ed27ea62'],
            [__DIR__ . '/qr-payment-information-without-amount-and-long-addresses.png', 'b374dca724328ac2587127406ce77369'],
            [__DIR__ . '/qr-payment-information-without-amount.png', '1da10251b49d72aa48c35207f54d4f1e'],
            [__DIR__ . '/qr-payment-information-without-amount-but-debtor.png', '44e4dc184fa2ab19517ae62edef58281'],
            [__DIR__ . '/qr-payment-information-zero-amount.png', 'd779c23775b755e7d193b22e93b51ed4'],
            [__DIR__ . '/qr-payment-reference-non.png', '176a87b6743ebb3b4d15e9d85937780a'],
            [__DIR__ . '/qr-payment-reference-scor.png', '7abd60316b137fa472165faff8e4a28c'],
            [__DIR__ . '/qr-ultimate-debtor.png', 'b60739f211a626720cee7990ada02633'],

            [__DIR__ . '/proof-of-validation.png', '86ef8e9bba53cc16193ee99e395f44e8'],
        ];
    }
}
