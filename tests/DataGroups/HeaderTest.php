<?php

namespace Sprain\SwissQrBill\Tests\DataGroups;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroups\Header;

class HeaderTest extends TestCase
{
    /**
     * @dataProvider validQrTypeProvider
     */
    public function testQrTypeIsValid($value)
    {
        $header = new Header();
        $header->setQrType($value);
        $header->setVersion('0100');
        $header->setCoding(1);

        $this->assertSame(0, $header->getViolations()->count());
    }

    public function validQrTypeProvider()
    {
        return [
            ['SPC'],
            ['foo'],
            ['123'],
            ['000'],
            ['A1B'],
            ['1AB'],
            ['AB1'],
        ];
    }

    /**
     * @dataProvider invalidQrTypeProvider
     */
    public function testQrTypeIsInvalid($value)
    {
        $header = new Header();
        $header->setQrType($value);
        $header->setVersion('0100');
        $header->setCoding(1);

        $this->assertSame(1, $header->getViolations()->count());
    }

    public function invalidQrTypeProvider()
    {
        return [
            ['SP'],
            ['SPCC'],
            ['fo'],
            ['fooo'],
            ['12'],
            ['00'],
            ['SP*'],
            ['*SP'],
        ];
    }

    public function testQrTypeIsRequired()
    {
        $header = new Header();
        $header->setVersion('0100');
        $header->setCoding(1);

        $this->assertSame(1, $header->getViolations()->count());
    }

    /**
     * @dataProvider validVersionProvider
     */
    public function testVersionIsValid($value)
    {
        $header = new Header();
        $header->setQrType('SPC');
        $header->setVersion($value);
        $header->setCoding(1);

        $this->assertSame(0, $header->getViolations()->count());
    }

    public function validVersionProvider()
    {
        return [
            ['0100'],
            ['1234'],
            ['0000'],
            ['9999'],
        ];
    }

    /**
     * @dataProvider invalidVersionProvider
     */
    public function testVersionIsInvalid($value)
    {
        $header = new Header();
        $header->setQrType('SPC');
        $header->setVersion($value);
        $header->setCoding(1);

        $this->assertSame(1, $header->getViolations()->count());
    }

    public function invalidVersionProvider()
    {
        return [
            ['010'],
            ['234'],
            ['ABCD'],
            ['abcd'],
            ['a1b2'],
            ['1a2b'],
            ['010*'],
            ['*010']
        ];
    }

    public function testVersionIsRequired()
    {
        $header = new Header();
        $header->setQrType('SPC');
        $header->setCoding(1);

        $this->assertSame(1, $header->getViolations()->count());
    }

    /**
     * @dataProvider validCodingProvider
     */
    public function testCodingIsValid($value)
    {
        $header = new Header();
        $header->setQrType('SPC');
        $header->setVersion('0100');
        $header->setCoding($value);

        $this->assertSame(0, $header->getViolations()->count());
    }

    public function validCodingProvider()
    {
        return [
            [0],
            [1],
            [2],
            [3],
            [4],
            [5],
            [6],
            [7],
            [8],
            [9],
        ];
    }

    /**
     * @dataProvider invalidCodingProvider
     */
    public function testCodingIsInvalid($value)
    {
        $header = new Header();
        $header->setQrType('SPC');
        $header->setVersion('0100');
        $header->setCoding($value);

        $this->assertSame(1, $header->getViolations()->count());
    }

    public function invalidCodingProvider()
    {
        return [
            [11],
            [222],
        ];
    }

    public function testCodingisRequired()
    {
        $header = new Header();
        $header->setQrType('SPC');
        $header->setVersion('0100');

        $this->assertSame(1, $header->getViolations()->count());
    }
}