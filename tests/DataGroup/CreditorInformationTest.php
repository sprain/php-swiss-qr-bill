<?php

namespace Sprain\SwissQrBill\Tests\DataGroups;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroup\CreditorInformation;

class CreditorInformationTest extends TestCase
{
    /**
     * @dataProvider validIbanProvider
     */
    public function testIbanIsValid($value)
    {
        $creditorInformation = new CreditorInformation();
        $creditorInformation->setIban($value);

        $this->assertSame(0, $creditorInformation->getViolations()->count());
    }

    public function validIbanProvider()
    {
        return [
            ['CH93 0076 2011 6238 5295 7'],
            ['CH9300762011623852957'],
            ['LI21 0881 0000 2324 013A A'],
            ['LI21088100002324013AA'],
        ];
    }

    /**
     * @dataProvider invalidIbanProvider
     */
    public function testIbanIsInvalid($value, $numberOfViolations)
    {
        $creditorInformation = new CreditorInformation();
        $creditorInformation->setIban($value);

        $this->assertSame($numberOfViolations, $creditorInformation->getViolations()->count());
    }

    public function invalidIbanProvider()
    {
        return [

            // missing number at end
            ['CH93 0076 2011 6238 5295', 1],
            ['CH930076201162385295', 1],
            ['LI21 0881 0000 2324 013A', 1],
            ['LI21088100002324013A', 1],

            // missing letter in front
            ['H93 0076 2011 6238 5295', 2],
            ['H930076201162385295', 2],
            ['I21 0881 0000 2324 013A', 2],
            ['I21088100002324013A', 2],

            // valid IBANs from unsupported countries
            ['AT61 1904 3002 3457 3201', 1],
            ['NO9386011117947', 1],

            // random strings
            ['foo', 2],
            ['123', 2],
            ['*', 2]
        ];
    }

    public function testIbanIsRequired()
    {
        $creditorInformation = new CreditorInformation();

        $this->assertSame(1, $creditorInformation->getViolations()->count());
    }

    public function testQrCodeData()
    {
        $creditorInformation = new CreditorInformation();
        $creditorInformation->setIban('CH9300762011623852957');

        $expected = [
            'CH9300762011623852957',
        ];

        $this->assertSame($expected, $creditorInformation->getQrCodeData());
    }
}