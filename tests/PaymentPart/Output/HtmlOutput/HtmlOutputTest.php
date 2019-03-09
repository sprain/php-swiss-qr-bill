<?php

namespace Sprain\Tests\SwissQrBill\PaymentPart\Output\HtmlOutput;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\HtmlOutput;
use Sprain\SwissQrBill\QrBill;
use Sprain\Tests\SwissQrBill\TestQrBillCreatorTrait;
use Symfony\Component\DomCrawler\Crawler;

class HtmlOutputTest extends TestCase
{
    use TestQrBillCreatorTrait;

    private $regenerateReferenceHtmlOutputs = false;

    /**
     * @dataProvider validQrBillsProvider
     */
    public function testValidQrBills(string $name, QrBill $qrBill)
    {
        $file = __DIR__ . '/../../../TestData/HtmlOutput/' . $name . '.html';

        $output = (new HtmlOutput($qrBill, 'en'))->getPaymentPart();

        if ($this->regenerateReferenceHtmlOutputs) {
           file_put_contents($file, $output);
        }

        $crawler = new Crawler($output);
        $this->assertEquals(1, $crawler->filter('td#qr-bill-payment-part')->count());

        $this->assertSame(
            file_get_contents($file),
            $output
        );
    }
}