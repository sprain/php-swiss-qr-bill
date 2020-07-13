<?php

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
            [__DIR__ . '/qr-additional-information.png', '3a23f70cc8cf519f66d27b73f002d828'],
            [__DIR__ . '/qr-alternative-schemes.png', '5bba5e41336c8e22fa50bea7627da41c'],
            [__DIR__ . '/qr-full-set.png', '766de4a121a49a7e88dacce508f1588f'],
            [__DIR__ . '/qr-minimal-setup.png', '069e274f22816fcdd799acb1c0cc1dd9'],
            [__DIR__ . '/qr-payment-information-without-amount.png', '5705acaab9b9219884fdb2bc99c2cfce'],
            [__DIR__ . '/qr-payment-information-without-amount-but-debtor.png', '281316342f09a85af4ad137dc4d30998'],
            [__DIR__ . '/qr-payment-information-zero-amount.png', 'aac0aef35bd36e9b79e903998db4756f'],
            [__DIR__ . '/qr-payment-reference-non.png', '2e1ebe0623baf0f52922c2e6b988bcc6'],
            [__DIR__ . '/qr-payment-reference-scor.png', '03daab8e7c66094bbef308236882c739'],
            [__DIR__ . '/qr-ultimate-debtor.png', '5c359ee3333833a54a2076e5e83d2d20'],

            [__DIR__ . '/proof-of-validation.png', '7d13a8739e490b3cdf13d7d86f66d6cb'],
        ];
    }
}