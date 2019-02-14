<?php

namespace Sprain\Tests\SwissQrBill\PaymentPart\Output\HtmlOutput;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\HtmlOutput;
use Sprain\Tests\SwissQrBill\TestQrBillCreatorTrait;
use Symfony\Component\DomCrawler\Crawler;

class HtmlOutputTest extends TestCase
{
    use TestQrBillCreatorTrait;

    /**
     * @dataProvider qrBillProvider
     */
    public function testOutputIsHtml($qrBill)
    {
        $output = (new HtmlOutput($qrBill, 'de'))->getPaymentPart();

        $crawler = new Crawler($output);
        $this->assertEquals(1, $crawler->filter('td#qr-bill-payment-part')->count());
    }

    public function qrBillProvider()
    {
        return [
            [
                $this->createQrBill([
                    'header',
                    'creditorInformationQrIban',
                    'creditor',
                    'paymentAmountInformation',
                    'paymentReferenceQr'
                ])
            ],
            [
                $this->createQrBill([
                    'header',
                    'creditorInformationQrIban',
                    'creditor',
                    'paymentAmountInformationWithoutAmount',
                    'paymentReferenceQr'
                ])
            ],
            [
                $this->createQrBill([
                    'header',
                    'creditorInformationIban',
                    'creditor',
                    'paymentAmountInformation',
                    'paymentReferenceScor'
                ])
            ],
            [
                $this->createQrBill([
                    'header',
                    'creditorInformationIban',
                    'creditor',
                    'paymentAmountInformation',
                    'paymentReferenceScor'
                ])
            ],
            [
                $this->createQrBill([
                    'header',
                    'creditorInformationIban',
                    'creditor',
                    'paymentAmountInformation',
                    'paymentReferenceNon'
                ])
            ],
            [
                $this->createQrBill([
                    'header',
                    'creditorInformationQrIban',
                    'creditor',
                    'paymentAmountInformation',
                    'paymentReferenceQr',
                    'ultimateDebtor'
                ])
            ],
            [
                $this->createQrBill([
                    'header',
                    'creditorInformationQrIban',
                    'creditor',
                    'paymentAmountInformation',
                    'paymentReferenceQr',
                ])
            ],
            [
                $this->createQrBill([
                    'header',
                    'creditorInformationQrIban',
                    'creditor',
                    'paymentAmountInformation',
                    'paymentReferenceQr',
                    'additionalInformation'
                ])
            ]
        ];
    }
}