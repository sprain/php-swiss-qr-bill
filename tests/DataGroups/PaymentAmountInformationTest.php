<?php

namespace Sprain\SwissQrBill\Tests\DataGroups;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroups\PaymentAmountInformation;

class PaymentAmountInformationTest extends TestCase
{
    /** @var PaymentAmountInformation */
    private $paymentAmountInformation;

    public function setUp()
    {
        // Valid default to be adjusted in single tests
        $this->paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25)
            ->setCurrency('CHF');
    }

    /**
     * @dataProvider validAmountProvider
     */
    public function testAmountIsValid($value)
    {
        $this->paymentAmountInformation->setAmount($value);

        $this->assertSame(0, $this->paymentAmountInformation->getViolations()->count());
    }

    public function validAmountProvider()
    {
        return [
            [null],
            [0],
            [11.11],
            [100.2],
            [999999999.99],
        ];
    }

    /**
     * @dataProvider invalidAmountProvider
     */
    public function testAmountIsInvalid($value)
    {
        $this->paymentAmountInformation->setAmount($value);

        $this->assertSame(1, $this->paymentAmountInformation->getViolations()->count());
    }

    public function invalidAmountProvider()
    {
        return [
            [-0.01],
            [1999999999.99],
            // [11.111], @todo: only two decimal places should be allowed
        ];
    }

    /**
     * @dataProvider validCurrencyProvider
     */
    public function testCurrencyIsValid($value)
    {
        $this->paymentAmountInformation->setCurrency($value);

        $this->assertSame(0, $this->paymentAmountInformation->getViolations()->count());
    }

    public function validCurrencyProvider()
    {
        return [
            ['CHF'],
            ['EUR'],
            ['chf'],
            ['eur']
        ];
    }

    /**
     * @dataProvider invalidCurrencyProvider
     */
    public function testCurrencyIsInvalid($value)
    {
        $this->paymentAmountInformation->setCurrency($value);

        $this->assertSame(1, $this->paymentAmountInformation->getViolations()->count());
    }

    public function invalidCurrencyProvider()
    {
        return [
            ['USD'],
            ['PLN'],
            [' chf '],
            [' EUR']
        ];
    }

    public function testQrCodeData()
    {
        $expected = [
            '25.00',
            'CHF'
        ];

        $this->assertSame($expected, $this->paymentAmountInformation->getQrCodeData());
    }
}