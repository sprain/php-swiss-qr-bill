<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill\DataGroup\Element;

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

    /**
     * @dataProvider formattedAmountProvider
     */
    public function testFormattedAmount($amount, $formattedAmount)
    {
        $paymentAmountInformation = PaymentAmountInformation::create(
            'CHF',
            $amount
        );

        $this->assertSame($formattedAmount, $paymentAmountInformation->getFormattedAmount());
    }

    public function formattedAmountProvider()
    {
        return [
            [0, '0.00'],
            [25, '25.00'],
            [1234.5, '1 234.50'],
            [1234.55, '1 234.55'],
            [12345.60, '12 345.60'],
            [1234567, '1 234 567.00'],
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