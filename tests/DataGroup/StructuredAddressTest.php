<?php

namespace Sprain\SwissQrBill\Tests\DataGroups;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroup\StructuredAddress;

class StructuredAddressTest extends TestCase
{
    /** @var StructuredAddress */
    private $address;

    public function setUp()
    {
        // Valid default to be adjusted in single tests
        $this->address = (new StructuredAddress())
            ->setName('Thomas Mustermann')
            ->setStreet('Musterweg')
            ->setBuildingNumber('22a')
            ->setPostalCode('1000')
            ->setCity('Lausanne')
            ->setCountry('CH');
    }

    /**
     * @dataProvider validStreetProvider
     */
    public function testStreetIsValid($value)
    {
        $this->address->setStreet($value);

        $this->assertSame(0, $this->address->getViolations()->count());
    }

    public function validStreetProvider()
    {
        return [
            [null],
            [''],
            ['A'],
            ['123'],
            ['Sonnenweg'],
            ['70 chars, character limit abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqr']
        ];
    }

    /**
     * @dataProvider invalidStreetProvider
     */
    public function testStreetIsInvalid($value)
    {
        $this->address->setStreet($value);

        $this->assertSame(1, $this->address->getViolations()->count());
    }

    public function invalidStreetProvider()
    {
        return [
            ['71 chars, above character limit abcdefghijklmnopqrstuvwxyzabcdefghijklm'],
        ];
    }

    /**
     * @dataProvider validbuildingNumberProvider
     */
    public function testbuildingNumberIsValid($value)
    {
        $this->address->setBuildingNumber($value);

        $this->assertSame(0, $this->address->getViolations()->count());
    }

    public function validbuildingNumberProvider()
    {
        return [
            [null],
            [''],
            ['1'],
            ['123'],
            ['22a'],
            ['16 chars, -limit']
        ];
    }

    /**
     * @dataProvider invalidbuildingNumberProvider
     */
    public function testbuildingNumberIsInvalid($value)
    {
        $this->address->setBuildingNumber($value);

        $this->assertSame(1, $this->address->getViolations()->count());
    }

    public function invalidbuildingNumberProvider()
    {
        return [
            ['17 chars, ++limit']
        ];
    }

    /**
     * @dataProvider validPostalCodeProvider
     */
    public function testPostalCodeIsValid($value)
    {
        $this->address->setPostalCode($value);

        $this->assertSame(0, $this->address->getViolations()->count());
    }

    public function validPostalCodeProvider()
    {
        return [
            ['1'],
            ['123'],
            ['22a'],
            ['16 chars, -limit']
        ];
    }

    /**
     * @dataProvider invalidPostalCodeProvider
     */
    public function testPostalCodeIsInvalid($value)
    {
        $this->address->setPostalCode($value);

        $this->assertSame(1, $this->address->getViolations()->count());
    }

    public function invalidPostalCodeProvider()
    {
        return [
            [''],
            ['17 chars, ++limit']
        ];
    }

    /**
     * @dataProvider validCityProvider
     */
    public function testCityIsValid($value)
    {
        $this->address->setCity($value);

        $this->assertSame(0, $this->address->getViolations()->count());
    }

    public function validCityProvider()
    {
        return [
            ['A'],
            ['Zürich'],
            ['35 chars, character limit abcdefghi']
        ];
    }

    /**
     * @dataProvider invalidCityProvider
     */
    public function testCityIsInvalid($value)
    {
        $this->address->setCity($value);

        $this->assertSame(1, $this->address->getViolations()->count());
    }

    public function invalidCityProvider()
    {
        return [
            [''],
            ['36 chars, above character limit abcd']
        ];
    }

    public function testQrCodeData()
    {
        $expected = [
            'S',
            'Thomas Mustermann',
            'Musterweg',
            '22a',
            '1000',
            'Lausanne',
            'CH',
        ];

        $this->assertSame($expected, $this->address->getQrCodeData());
    }

    /**
     * @dataProvider addressProvider
     */
    public function testFullAddressString(StructuredAddress $address, $expected)
    {
        $this->assertSame($expected, $address->getFullAddress());
    }

    public function addressProvider()
    {
        return [
            [
                $this->address = (new StructuredAddress())
                    ->setName('Thomas Mustermann')
                    ->setStreet('Musterweg')
                    ->setBuildingNumber('22a')
                    ->setPostalCode('1000')
                    ->setCity('Lausanne')
                    ->setCountry('CH'),
                "Thomas Mustermann\nMusterweg 22a\nCH-1000 Lausanne"
            ],
            [
                $this->address = (new StructuredAddress())
                    ->setName('Thomas Mustermann')
                    ->setStreet('Musterweg')
                    ->setPostalCode('1000')
                    ->setCity('Lausanne')
                    ->setCountry('CH'),
                "Thomas Mustermann\nMusterweg\nCH-1000 Lausanne"
            ],
            [
                $this->address = (new StructuredAddress())
                    ->setName('Thomas Mustermann')
                    ->setPostalCode('1000')
                    ->setCity('Lausanne')
                    ->setCountry('CH'),
                "Thomas Mustermann\nCH-1000 Lausanne"
            ],
            [
                $this->address = (new StructuredAddress())
                    ->setName('Thomas Mustermann')
                    ->setBuildingNumber('22a')
                    ->setPostalCode('1000')
                    ->setCity('Lausanne')
                    ->setCountry('CH'),
                "Thomas Mustermann\nCH-1000 Lausanne"
            ]
        ];
    }
}