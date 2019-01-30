<?php

namespace Sprain\SwissQrBill\Tests\DataGroups;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroup\PaymentReference;

class PaymentReferenceTest extends TestCase
{
    public function testValidQrReference()
    {
        $paymentReference = new PaymentReference();
        $paymentReference->setType(PaymentReference::TYPE_QR);
        $paymentReference->setReference('012345678901234567890123456');

        $this->assertSame(0, $paymentReference->getViolations()->count());
    }

    /**
     * @dataProvider invalidQrReferenceProvider
     */
    public function testInvalidQrReference($value)
    {
        $paymentReference = new PaymentReference();
        $paymentReference->setType(PaymentReference::TYPE_QR);
        $paymentReference->setReference($value);

        $this->assertSame(1, $paymentReference->getViolations()->count());
    }

    public function invalidQrReferenceProvider()
    {
        return [
            [null],
            ['01234567890123456789012345'],   // too short
            ['0123456789012345678901234567'], // too long
            ['Ä12345678901234567890123456']   // invalid characters
        ];
    }

    public function testValidScorReference()
    {
        $paymentReference = new PaymentReference();
        $paymentReference->setType(PaymentReference::TYPE_SCOR);
        $paymentReference->setReference('RF18539007547034');

        $this->assertSame(0, $paymentReference->getViolations()->count());
    }

    /**
     * @dataProvider invalidScorReferenceProvider
     */
    public function testInvalidScorReference($value)
    {
        $paymentReference = new PaymentReference();
        $paymentReference->setType(PaymentReference::TYPE_SCOR);
        $paymentReference->setReference($value);

        $this->assertSame(1, $paymentReference->getViolations()->count());
    }

    public function invalidScorReferenceProvider()
    {
        return [
            [null],
            ['RF12'],// too short
            ['RF181234567890123456789012'], // too long
            ['RF1853900754703Ä']  // invalid characters
        ];
    }

    public function testValidNonReference()
    {
        $paymentReference = new PaymentReference();
        $paymentReference->setType(PaymentReference::TYPE_NON);
        $paymentReference->setReference(null);

        $this->assertSame(0, $paymentReference->getViolations()->count());
    }

    /**
     * @dataProvider invalidNonReferenceProvider
     */
    public function testInvalidNonReference($value)
    {
        $paymentReference = new PaymentReference();
        $paymentReference->setType(PaymentReference::TYPE_NON);
        $paymentReference->setReference($value);

        $this->assertSame(1, $paymentReference->getViolations()->count());
    }

    public function invalidNonReferenceProvider()
    {
        return [
            ['anything-non-empty'],
            [' '],
            [0]
        ];
    }

    public function testQrCodeData()
    {
        $paymentReference = new PaymentReference();
        $paymentReference->setType(PaymentReference::TYPE_QR);
        $paymentReference->setReference('012345678901234567890123456');

        $expected = [
            PaymentReference::TYPE_QR,
            '012345678901234567890123456'
        ];

        $this->assertSame($expected, $paymentReference->getQrCodeData());
    }
}