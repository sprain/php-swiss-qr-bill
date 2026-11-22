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
            [__DIR__ . '/qr-payment-information-with-mediumlong-creditor-and-unknown-debtor.png', 'c8f3ea4e3a06624b8b0429872c14f0b0'],
            [__DIR__ . '/qr-payment-information-without-amount-and-long-addresses.png', '310db0f82443ef471efd37d982405b80'],
            [__DIR__ . '/qr-payment-information-without-amount.png', 'bdccbaec3eedebc422ed8b011934bfda'],
            [__DIR__ . '/qr-payment-information-without-amount-but-debtor.png', '6deea4e53e5c7f819c06f8d2f61712c9'],
            [__DIR__ . '/qr-payment-information-zero-amount.png', '65a0f8ae26baab93eaad6370a3ab0477'],
            [__DIR__ . '/qr-payment-reference-non.png', '176a87b6743ebb3b4d15e9d85937780a'],
            [__DIR__ . '/qr-payment-reference-scor.png', '7abd60316b137fa472165faff8e4a28c'],
            [__DIR__ . '/qr-payment-reference-scor-eur.png', 'a819ff1f0c3e88d857a9e9b67ca72719'],
            [__DIR__ . '/qr-ultimate-debtor.png', 'b60739f211a626720cee7990ada02633'],

            [__DIR__ . '/proof-of-validation.png', 'b8b467ac31940ab6f6449f8876103f4e'],
        ];
    }
}
