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
            [__DIR__ . '/qr-additional-information.png', 'e5a630cb9e59a2219a4b4212f787f66b'],
            [__DIR__ . '/qr-alternative-schemes.png', 'ca22587f45609486ec9128f8bfb9ef83'],
            [__DIR__ . '/qr-full-set.png', 'ae3aa21373bb4b6ad61a8df96995f06b'],
            [__DIR__ . '/qr-international-ultimate-debtor.png', 'c8585f93e5f0f5f7c89c8c1ee2e89eda'],
            [__DIR__ . '/qr-minimal-setup.png', 'e0186a62a5d4ffeaa66903e0587cd6e8'],
            [__DIR__ . '/qr-payment-information-without-amount.png', 'aa02af5ebfa9d79067590d273fb1a063'],
            [__DIR__ . '/qr-payment-information-without-amount-but-debtor.png', '0e664c61803b33ceb7e5f687c748b001'],
            [__DIR__ . '/qr-payment-information-zero-amount.png', 'ef897e7a872b684e06304dcc83e83ddc'],
            [__DIR__ . '/qr-payment-reference-non.png', 'b7a3423d03db5b8d581f14ad56efd35f'],
            [__DIR__ . '/qr-payment-reference-scor.png', 'a79f86815a768d724132066d2c4490fb'],
            [__DIR__ . '/qr-ultimate-debtor.png', '151cc97a1fd8059db0f3276e0e530269'],

            [__DIR__ . '/proof-of-validation.png', '5a2ef1da7ce12c26b6980b73ce756cb7'],
        ];
    }
}