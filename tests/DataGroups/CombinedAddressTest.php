<?php

namespace Sprain\SwissQrBill\Tests\DataGroups;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroups\CombinedAddress;

class CombinedAddressTest extends TestCase
{
    /** @var CombinedAddress */
    private $address;

    public function setUp()
    {
        // Valid default to be adjusted in single tests
        $this->address = (new CombinedAddress())
            ->setName('Thomas Mustermann')
            ->setAddressLine1('Musterweg 22a')
            ->setAddressLine2('1000 Lausanne')
            ->setCountry('CH');
    }

    /**
     * @dataProvider validAddressLine1Provider
     */
    public function testAddressLine1IsValid($value)
    {
        $this->address->setAddressLine1($value);

        $this->assertSame(0, $this->address->getViolations()->count());
    }

    public function validAddressLine1Provider()
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
     * @dataProvider invalidAddressLine1Provider
     */
    public function testAddressLine1IsInvalid($value)
    {
        $this->address->setAddressLine1($value);

        $this->assertSame(1, $this->address->getViolations()->count());
    }

    public function invalidAddressLine1Provider()
    {
        return [
            ['71 chars, above character limit abcdefghijklmnopqrstuvwxyzabcdefghijklm'],
        ];
    }

    /**
     * @dataProvider validAddressLine2Provider
     */
    public function testAddressLine2IsValid($value)
    {
        $this->address->setAddressLine2($value);

        $this->assertSame(0, $this->address->getViolations()->count());
    }

    public function validAddressLine2Provider()
    {
        return [
            ['A'],
            ['123'],
            ['Sonnenweg'],
            ['70 chars, character limit abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqr']
        ];
    }

    /**
     * @dataProvider invalidAddressLine2Provider
     */
    public function testAddressLine2IsInvalid($value)
    {
        $this->address->setAddressLine2($value);

        $this->assertSame(1, $this->address->getViolations()->count());
    }

    public function invalidAddressLine2Provider()
    {
        return [
            ['71 chars, above character limit abcdefghijklmnopqrstuvwxyzabcdefghijklm'],
        ];
    }

    public function testAddressLine2IsRequired()
    {
        $this->address->setAddressLine2('');

        $this->assertSame(1, $this->address->getViolations()->count());
    }

    public function testQrCodeData()
    {
        $expected = [
            'K',
            'Thomas Mustermann',
            'Musterweg 22a',
            '1000 Lausanne',
            '',
            '',
            'CH',
        ];

        $this->assertSame($expected, $this->address->getQrCodeData());
    }

    /**
     * @dataProvider addressProvider
     */
    public function testFullAddressString(CombinedAddress $address, $expected)
    {
        $this->assertSame($expected, $address->getFullAddress());
    }

    public function addressProvider()
    {
        return [
            [
                $this->address = (new CombinedAddress())
                    ->setName('Thomas Mustermann')
                    ->setAddressLine1('Musterweg 22a')
                    ->setAddressLine2('1000 Lausanne')
                    ->setCountry('CH'),
                "Thomas Mustermann\nMusterweg 22a\nCH-1000 Lausanne"
            ],
            [
                $this->address = (new CombinedAddress())
                    ->setName('Thomas Mustermann')
                    ->setAddressLine2('1000 Lausanne')
                    ->setCountry('CH'),
                "Thomas Mustermann\nCH-1000 Lausanne"
            ],
        ];
    }
}