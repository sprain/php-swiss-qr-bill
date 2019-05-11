<?php

namespace Sprain\Tests\SwissQrBill\DataGroup\Element;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroup\Element\PaymentReference;

class PaymentReferenceTest extends TestCase
{
    /**
     * @dataProvider qrReferenceProvider
     */
    public function testQrReference($numberOfViolations, $value)
    {
        $paymentReference = PaymentReference::create(
            PaymentReference::TYPE_QR,
            $value
        );

        $this->assertSame($numberOfViolations, $paymentReference->getViolations()->count());
    }

    public function qrReferenceProvider()
    {
        return [
            [0, '012345678901234567890123456'],
            [1, null],
            [1, '01234567890123456789012345'],   // too short
            [1, '0123456789012345678901234567'], // too long
            [1, 'Ä12345678901234567890123456']   // invalid characters
        ];
    }

    /**
     * @dataProvider scorReferenceProvider
     */
    public function testScorReference($numberOfViolations, $value)
    {
        $paymentReference = PaymentReference::create(
            PaymentReference::TYPE_SCOR,
            $value
        );

        $this->assertSame($numberOfViolations, $paymentReference->getViolations()->count());
    }

    public function scorReferenceProvider()
    {
        return [
            [0, 'RF18539007547034'],
            [1, null],
            [1, 'RF12'],// too short
            [1, 'RF181234567890123456789012'], // too long
            [1, 'RF1853900754703Ä']  // invalid characters
        ];
    }

    /**
     * @dataProvider nonReferenceProvider
     */
    public function testNonReference($numberOfViolations, $value)
    {
        $paymentReference = PaymentReference::create(
            PaymentReference::TYPE_NON,
            $value
        );

        $this->assertSame($numberOfViolations, $paymentReference->getViolations()->count());
    }

    public function nonReferenceProvider()
    {
        return [
            [0, null],
            [1, 'anything-non-empty'],
            [1, ' '],
            [1, 0]
        ];
    }

    /**
     * @dataProvider formattedReferenceProvider
     */
    public function testFormattedReference($type, $reference, $formattedReference)
    {
        $paymentReference = PaymentReference::create(
            $type,
            $reference
        );

        $this->assertSame($formattedReference, $paymentReference->getFormattedReference());
    }

    public function formattedReferenceProvider()
    {
        return [
            [PaymentReference::TYPE_QR, '012345678901234567890123456', '01 23456 78901 23456 78901 23456'],
            [PaymentReference::TYPE_SCOR, 'RF18539007547034', 'RF18 5390 0754 7034'],
            [PaymentReference::TYPE_NON, null, null],
        ];
    }

    public function testQrCodeData()
    {
        $paymentReference = PaymentReference::create(
            PaymentReference::TYPE_QR,
            '012345678901234567890123456'
        );

        $expected = [
            PaymentReference::TYPE_QR,
            '012345678901234567890123456'
        ];

        $this->assertSame($expected, $paymentReference->getQrCodeData());
    }
}