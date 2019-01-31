<?php

namespace Sprain\SwissQrBill\Tests\DataGroup\Element;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroup\Element\AlternativeScheme;

class AlternativeSchemeTest extends TestCase
{
    /**
     * @dataProvider validParameterProvider
     */
    public function testParameterIsValid($value)
    {
        $alternativeScheme = new AlternativeScheme();
        $alternativeScheme->setParameter($value);

        $this->assertSame(0, $alternativeScheme->getViolations()->count());
    }

    public function validParameterProvider()
    {
        return [
            ['1'],
            ['foo'],
            ['1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890'],

            // examples as shown in https://www.paymentstandards.ch/dam/downloads/qrcodegenerator.java
            ['1;1.1;1278564;1A-2F-43-AC-9B-33-21-B0-CC-D4-28-56;TCXVMKC22;2017-02-10T15:12:39;2017-02-10T15:18:16'],
            ['2;2a-2.2r;_R1-CH2_ConradCH-2074-1_3350_2017-03-13T10:23:47_16,99_0,00_0,00_0,00_0,00_+8FADt/DQ=_1==']
        ];
    }

    /**
     * @dataProvider invalidParameterProvider
     */
    public function testParameterIsInvalid($value, $numberOfViolations)
    {
        $alternativeScheme = new AlternativeScheme();
        $alternativeScheme->setParameter($value);

        $this->assertSame($numberOfViolations, $alternativeScheme->getViolations()->count());
    }

    public function invalidParameterProvider()
    {
        return [
            ['', 1],
            ['12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901', 1] // too long
        ];
    }

    public function testParameterIsRequired()
    {
        $alternativeScheme = new AlternativeScheme();
        
        $this->assertSame(1, $alternativeScheme->getViolations()->count());
    }
}