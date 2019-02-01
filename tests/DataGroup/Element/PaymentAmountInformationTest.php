<?php

namespace Sprain\SwissQrBill\Tests\DataGroup\Element;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroup\Element\PaymentAmountInformation;

class PaymentAmountInformationTest extends TestCase
{
    /**
     * @dataProvider amountProvider
     */
    public function testAmount($numberOfViolations, $value)
    {
        $paymentAmountInformation = PaymentAmountInformation::create(
            'CHF',
            $value
        );

        $this->assertSame($numberOfViolations, $paymentAmountInformation->getViolations()->count());
    }

    public function amountProvider()
    {
        return [
            [0, null],
            [0, 0],
            [0, 11.11],
            [0, 100.2],
            [0, 999999999.99],
            [1, -0.01],
            [1, 1999999999.99],
            // [1, 11.111], @todo: only two decimal places should be allowed
        ];
    }

    /**
     * @dataProvider currencyProvider
     */
    public function testCurrency($numberOfViolations, $value)
    {
        $paymentAmountInformation = PaymentAmountInformation::create(
            $value,
            25
        );

        $this->assertSame($numberOfViolations, $paymentAmountInformation->getViolations()->count());
    }

    public function currencyProvider()
    {
        return [
            [0, 'CHF'],
            [0, 'EUR'],
            [0, 'chf'],
            [0, 'eur'],
            [1, 'USD'],
            [1, 'PLN'],
            [1, ' chf '],
            [1, ' EUR']
        ];
    }

    public function testQrCodeData()
    {
        $paymentAmountInformation = PaymentAmountInformation::create(
            'CHF',
            25
        );

        $expected = [
            '25.00',
            'CHF'
        ];

        $this->assertSame($expected, $paymentAmountInformation->getQrCodeData());
    }
}