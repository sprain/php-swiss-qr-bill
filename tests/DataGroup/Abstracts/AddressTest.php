<?php

namespace Sprain\SwissQrBill\Tests\DataGroups;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroup\Abstracts\Address;

class AddressTest extends TestCase
{
    /** @var Address */
    private $address;

    public function setUp()
    {
        // Valid default to be adjusted in single tests
        $this->address = ($this->getMockForAbstractClass(Address::class))
            ->setName('Thomas Mustermann')
            ->setCountry('CH');
    }

    /**
     * @dataProvider validNameProvider
     */
    public function testNameIsValid($value)
    {
        $this->address->setName($value);

        $this->assertSame(0, $this->address->getViolations()->count());
    }

    public function validNameProvider()
    {
        return [
            ['A'],
            ['123'],
            ['Müller AG'],
            ['Maria Bernasconi'],
            ['70 chars, character limit abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqr']
        ];
    }

    /**
     * @dataProvider invalidNameProvider
     */
    public function testNameIsInvalid($value)
    {
        $this->address->setName($value);

        $this->assertSame(1, $this->address->getViolations()->count());
    }

    public function invalidNameProvider()
    {
        return [
            [''],
            ['71 chars, above character limit abcdefghijklmnopqrstuvwxyzabcdefghijklm'],
        ];
    }

    /**
     * @dataProvider validCountryProvider
     */
    public function testCountryIsValid($value)
    {
        $this->address->setCountry($value);

        $this->assertSame(0, $this->address->getViolations()->count());
    }

    public function validCountryProvider()
    {
        return [
            ['CH'],
            ['ch'],
            ['DE'],
            ['LI'],
            ['US']
        ];
    }

    /**
     * @dataProvider invalidCountryProvider
     */
    public function testCountryIsInvalid($value)
    {
        $this->address->setCountry($value);

        $this->assertSame(1, $this->address->getViolations()->count());
    }

    public function invalidCountryProvider()
    {
        return [
            [''],
            ['XX'],
            ['SUI'],
            ['12']
        ];
    }
}