<?php

namespace Sprain\Tests\SwissQrBill\Reference;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\Reference\QrPaymentReferenceGenerator;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QrPaymentReferenceGeneratorTest extends TestCase
{
    /** @var  ValidatorInterface */
    private $validator;

    public function setUp()
    {
        $this->validator = Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata')
            ->getValidator();
    }

    /**
     * @dataProvider qrPaymentReferenceProvider
     */
    public function testQrPaymentReference($customerIdentification, $referenceNumber, $expectedResult)
    {
        $qrReference = QrPaymentReferenceGenerator::generate(
            $customerIdentification,
            $referenceNumber
        );

        $this->assertSame($expectedResult, $qrReference);
    }

    public function qrPaymentReferenceProvider()
    {
        return [
            // Realistic real-life examples
            ['310014', '18310019779911119', '310014000183100197799111196'], // https://www.tkb.ch/download/online/BESR-Handbuch.pdf
            ['040329', '340 ', '040329000000000000000003406'], // https://www.lukb.ch/documents/10620/13334/LUKB-BESR-Handbuch.pdf
            ['247656', '3073000002311006 ', '247656000030730000023110061'], // https://hilfe.flexbuero.ch/article/1181/
            ['123456', '11223344', '123456000000000000112233440'],

            // Handle it as numerics as well
            [310014, 18310019779911119, '310014000183100197799111196'],

            // Correct handling of whitespace
            [' 310 014 ', ' 1831001 9779911119 ', '310014000183100197799111196'],
        ];
    }

    /**
     * @dataProvider invalidCustomerIdentificationNumberProvider
     * @expectedException Sprain\SwissQrBill\Validator\Exception\InvalidQrPaymentReferenceException
     */
    public function testInvalidCustomerIdentificationNumber($value)
    {
        $qrReference = QrPaymentReferenceGenerator::generate(
            $value,
            '18310019779911119'
        );
    }

    public function invalidCustomerIdentificationNumberProvider()
    {
        return [
            ['1234567'], // too long
            ['12345A'],  // non-digits
            ['1234.5'],  // non-digits
            ['']
        ];
    }

    /**
     * @dataProvider invalidReferenceNumberProvider
     * @expectedException Sprain\SwissQrBill\Validator\Exception\InvalidQrPaymentReferenceException
     */
    public function testInvalidReferenceNumber($value)
    {
        $qrReference = QrPaymentReferenceGenerator::generate(
            '123456',
            $value
        );
    }

    public function invalidReferenceNumberProvider()
    {
        return [
            ['123456789012345678901'], // too long
            ['1234567890123456789A'],  // non-digits
            ['123456789012345678.0'],  // non-digits
            ['']
        ];
    }
}