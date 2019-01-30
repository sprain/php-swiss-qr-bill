<?php

namespace Sprain\SwissQrBill\Tests\DataGroups;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroup\AdditionalInformation;
use Sprain\SwissQrBill\DataGroup\StructuredAddress;
use Sprain\SwissQrBill\DataGroup\AlternativeScheme;

class AdditionalInformationTest extends TestCase
{
    /**
     * @dataProvider validMessageProvider
     */
    public function testValidMessage($value)
    {
        $additionalInformation = new AdditionalInformation();
        $additionalInformation->setMessage($value);

        $this->assertSame(0, $additionalInformation->getViolations()->count());
    }

    public function validMessageProvider()
    {
        return [
            ['012345678901234567890123456'],
            [null]
        ];
    }

    /**
     * @dataProvider invalidMessageProvider
     */
    public function testInvalidMessage($value)
    {
        $additionalInformation = new AdditionalInformation();
        $additionalInformation->setMessage($value);

        $this->assertSame(1, $additionalInformation->getViolations()->count());
    }

    public function invalidMessageProvider()
    {
        return [
            ['123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901'], // too long
        ];
    }

    /**
     * @dataProvider validBillInformationProvider
     */
    public function testValidBillInformation($value)
    {
        $additionalInformation = new AdditionalInformation();
        $additionalInformation->setBillInformation($value);

        $this->assertSame(0, $additionalInformation->getViolations()->count());
    }

    public function validBillInformationProvider()
    {
        return [
            ['012345678901234567890123456'],
            [null]
        ];
    }

    /**
     * @dataProvider invalidBillInformationProvider
     */
    public function testInvalidBillInformation($value)
    {
        $additionalInformation = new AdditionalInformation();
        $additionalInformation->setBillInformation($value);

        $this->assertSame(1, $additionalInformation->getViolations()->count());
    }

    public function invalidBillInformationProvider()
    {
        return [
            ['123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901'], // too long
        ];
    }

    public function testQrCodeData()
    {
        $additionalInformation = new AdditionalInformation();
        $additionalInformation->setMessage('message');
        $additionalInformation->setBillInformation('billInformation');

        $expected = [
            'message',
            AdditionalInformation::TRAILER_EPD,
            'billInformation'
        ];

        $this->assertSame($expected, $additionalInformation->getQrCodeData());
    }
}