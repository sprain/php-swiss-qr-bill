<?php

namespace Sprain\Tests\SwissQrBill\DataGroup\Element;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroup\Element\CombinedAddress;

class CombinedAddressTest extends TestCase
{
    /**
     * @dataProvider nameProvider
     */
    public function testName($numberOfValidations, $value)
    {
        $address = CombinedAddress::create(
            $value,
            'Musterweg 22a',
            '1000 Lausanne',
            'CH'
        );

        $this->assertSame($numberOfValidations, $address->getViolations()->count());
    }

    public function nameProvider()
    {
        return [
            [0, 'A'],
            [0, '123'],
            [0, 'Müller AG'],
            [0, 'Maria Bernasconi'],
            [0, '70 chars, character limit abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqr'],
            [1, ''],
            [1, '71 chars, above character limit abcdefghijklmnopqrstuvwxyzabcdefghijklm']
        ];
    }

    /**
     * @dataProvider addressLine1Provider
     */
    public function testAddressLine1($numberOfValidations, $value)
    {
        $address = CombinedAddress::create(
            'Thomas Mustermann',
            $value,
            '1000 Lausanne',
            'CH'
        );

        $this->assertSame($numberOfValidations, $address->getViolations()->count());
    }

    public function addressLine1Provider()
    {
        return [
            [0, null],
            [0, ''],
            [0, 'A'],
            [0, '123'],
            [0, 'Sonnenweg'],
            [0, '70 chars, character limit abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqr'],
            [1, '71 chars, above character limit abcdefghijklmnopqrstuvwxyzabcdefghijklm']
        ];
    }

    /**
     * @dataProvider addressLine2Provider
     */
    public function testAddressLine2($numberOfValidations, $value)
    {
        $address = CombinedAddress::create(
            'Thomas Mustermann',
            'Musterweg 22a',
            $value,
            'CH'
        );

        $this->assertSame($numberOfValidations, $address->getViolations()->count());
    }

    public function addressLine2Provider()
    {
        return [
            [0, 'A'],
            [0, '123'],
            [0, 'Sonnenweg'],
            [0, '70 chars, character limit abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqr'],
            [1, ''],
            [1, '71 chars, above character limit abcdefghijklmnopqrstuvwxyzabcdefghijklm']
        ];
    }

    public function testQrCodeData()
    {
        $address = CombinedAddress::create(
            'Thomas Mustermann',
            'Musterweg 22a',
            '1000 Lausanne',
            'CH'
        );

        $expected = [
            'K',
            'Thomas Mustermann',
            'Musterweg 22a',
            '1000 Lausanne',
            '',
            '',
            'CH',
        ];

        $this->assertSame($expected, $address->getQrCodeData());
    }

    /**
     * @dataProvider countryProvider
     */
    public function testCountry($numberOfValidations, $value)
    {
        $address = CombinedAddress::create(
            'Thomas Mustermann',
            'Musterweg 22a',
            '1000 Lausanne',
            $value
        );

        $this->assertSame($numberOfValidations, $address->getViolations()->count());
    }

    public function countryProvider()
    {
        return [
            [0, 'CH'],
            [0, 'ch'],
            [0, 'DE'],
            [0, 'LI'],
            [0, 'US'],
            [1, ''],
            [1, 'XX'],
            [1, 'SUI'],
            [1, '12']
        ];
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
                CombinedAddress::create(
                    'Thomas Mustermann',
                    'Musterweg 22a',
                    '1000 Lausanne',
                    'CH'
                ),
                "Thomas Mustermann\nMusterweg 22a\nCH-1000 Lausanne"
            ],
            [
                CombinedAddress::create(
                    'Thomas Mustermann',
                    null,
                    '1000 Lausanne',
                    'CH'
                ),
                "Thomas Mustermann\nCH-1000 Lausanne"
            ],
        ];
    }
}