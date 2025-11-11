<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill\Reference;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\Reference\QrPaymentReferenceGenerator;
use Sprain\SwissQrBill\Validator\Exception\InvalidQrPaymentReferenceException;

final class QrPaymentReferenceGeneratorTest extends TestCase
{
    #[DataProvider('qrPaymentReferenceProvider')]
    public function testMakesResultsViaConstructor(?string $customerIdentification, string $referenceNumber, string $expectedResult): void
    {
        $qrReference = QrPaymentReferenceGenerator::generate(
            $customerIdentification,
            $referenceNumber
        );

        $this->assertSame($expectedResult, $qrReference);
    }

    #[DataProvider('qrPaymentReferenceProvider')]
    public function testMakesResultsViaFacade(?string $customerIdentification, string $referenceNumber, string $expectedResult): void
    {
        $qrReference = QrPaymentReferenceGenerator::generate(
            $customerIdentification,
            $referenceNumber
        );

        $this->assertSame($expectedResult, $qrReference);
    }

    public static function qrPaymentReferenceProvider(): array
    {
        return [
            // Real-life examples
            ['310014', '18310019779911119', '310014000183100197799111196'],
            ['040329', '340 ', '040329000000000000000003406'],
            ['247656', '3073000002311006 ', '247656000030730000023110061'],

            // More allowed combinations
            ['123456', '11223344', '123456000000000000112233440'],
            ['1234567890', '11223344', '123456789000000000112233444'],
            ['1234', '11223344', '123400000000000000112233449'],
            ['000000', '11223344', '000000000000000000112233442'],
            ['', '11223344', '000000000000000000112233442'],
            [null, '11223344', '000000000000000000112233442'],
            ['1234', '000000', '123400000000000000000000001'],

            // Correct handling of whitespace
            [' 310 014 ', ' 1831001 9779911119 ', '310014000183100197799111196'],
        ];
    }

    #[DataProvider('invalidQrPaymentReferenceProvider')]
    public function testInvalidQrPaymentReference(?string $customerIdentification, string $referenceNumber): void
    {
        $this->expectException(InvalidQrPaymentReferenceException::class);

        QrPaymentReferenceGenerator::generate(
            $customerIdentification,
            $referenceNumber
        );
    }

    public static function invalidQrPaymentReferenceProvider(): array
    {
        return [
            ['1234', '12345678901234567890123'], // too long in total
            ['123456', '123456789012345678901'], // too long in total
            ['12345678901', '1234567890123456'], // too long in total
            [null, '123456789012345678901234567'], // too long in total
            [null, '00000000'], // only zeros
            [null, '0'],        // only zeros
            ['00', '00'],       // only zeros
        ];
    }

    #[DataProvider('invalidCustomerIdentificationNumberProvider')]
    public function testInvalidCustomerIdentificationNumber(string $value): void
    {
        $this->expectException(InvalidQrPaymentReferenceException::class);

        QrPaymentReferenceGenerator::generate(
            $value,
            '18310019779911119'
        );
    }

    public static function invalidCustomerIdentificationNumberProvider(): array
    {
        return [
            ['123456789012'], // too long
            ['12345A'],  // non-digits
            ['1234.5'],  // non-digits
        ];
    }

    #[DataProvider('invalidReferenceNumberProvider')]
    public function testInvalidReferenceNumber(string $value): void
    {
        $this->expectException(InvalidQrPaymentReferenceException::class);
        
        QrPaymentReferenceGenerator::generate(
            '123456',
            $value
        );
    }

    public static function invalidReferenceNumberProvider(): array
    {
        return [
            ['1234567890123456789A'],  // non-digits
            ['123456789012345678.0'],  // non-digits
            ['']
        ];
    }
}
