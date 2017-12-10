<?php

namespace Sprain\SwissQrBill\Tests\DataGroups;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroups\Creditor;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreditorTest extends TestCase
{
    /** @var  ValidatorInterface */
    private $validator;

    /** @var Creditor */
    private $creditor;

    public function setUp()
    {
        $this->validator = Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata')
            ->getValidator();

        // Valid default to be adjusted in single tests
        $this->creditor = (new Creditor())
            ->setName('Thomas Mustermann')
            ->setStreet('Musterweg')
            ->setHouseNumber('22a')
            ->setPostalCode('1000')
            ->setCity('Lausanne')
            ->setCountry('CH');
    }

    /**
     * @dataProvider validNameProvider
     */
    public function testNameIsValid($value)
    {
        $this->creditor->setName($value);

        $this->assertSame(0, $this->validator->validate($this->creditor)->count());
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
        $this->creditor->setName($value);

        $this->assertSame(1, $this->validator->validate($this->creditor)->count());
    }

    public function invalidNameProvider()
    {
        return [
            [''],
            ['71 chars, above character limit abcdefghijklmnopqrstuvwxyzabcdefghijklm'],
        ];
    }

    /**
     * @dataProvider validStreetProvider
     */
    public function testStreetIsValid($value)
    {
        $this->creditor->setStreet($value);

        $this->assertSame(0, $this->validator->validate($this->creditor)->count());
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
        $this->creditor->setStreet($value);

        $this->assertSame(1, $this->validator->validate($this->creditor)->count());
    }

    public function invalidStreetProvider()
    {
        return [
            ['71 chars, above character limit abcdefghijklmnopqrstuvwxyzabcdefghijklm'],
        ];
    }

    /**
     * @dataProvider validHouseNumberProvider
     */
    public function testHouseNumberIsValid($value)
    {
        $this->creditor->setHouseNumber($value);

        $this->assertSame(0, $this->validator->validate($this->creditor)->count());
    }

    public function validHouseNumberProvider()
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
     * @dataProvider invalidHouseNumberProvider
     */
    public function testHouseNumberIsInvalid($value)
    {
        $this->creditor->setHouseNumber($value);

        $this->assertSame(1, $this->validator->validate($this->creditor)->count());
    }

    public function invalidHouseNumberProvider()
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
        $this->creditor->setPostalCode($value);

        $this->assertSame(0, $this->validator->validate($this->creditor)->count());
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
        $this->creditor->setPostalCode($value);

        $this->assertSame(1, $this->validator->validate($this->creditor)->count());
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
        $this->creditor->setCity($value);

        $this->assertSame(0, $this->validator->validate($this->creditor)->count());
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
        $this->creditor->setCity($value);

        $this->assertSame(1, $this->validator->validate($this->creditor)->count());
    }

    public function invalidCityProvider()
    {
        return [
            [''],
            ['36 chars, above character limit abcd']
        ];
    }

    /**
     * @dataProvider validCountryProvider
     */
    public function testCountryIsValid($value)
    {
        $this->creditor->setCountry($value);

        $this->assertSame(0, $this->validator->validate($this->creditor)->count());
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
        $this->creditor->setCountry($value);

        $this->assertSame(1, $this->validator->validate($this->creditor)->count());
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