<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill\Constraints;

use PHPUnit\Framework\Attributes\DataProvider;
use Sprain\SwissQrBill\Constraint\ValidCreditorInformationPaymentReferenceCombination;
use Sprain\SwissQrBill\Constraint\ValidCreditorInformationPaymentReferenceCombinationValidator;
use Sprain\SwissQrBill\DataGroup\Element\CreditorInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentReference;
use Sprain\SwissQrBill\QrBill;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

final class ValidCreditorInformationPaymentReferenceCombinationTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new ValidCreditorInformationPaymentReferenceCombinationValidator();
    }

    public function testNullIsValid()
    {
        $this->validator->validate(null, new ValidCreditorInformationPaymentReferenceCombination());

        $this->assertNoViolation();
    }

    public function testRandomClassIsValid()
    {
        $this->validator->validate(new \stdClass(), new ValidCreditorInformationPaymentReferenceCombination());

        $this->assertNoViolation();
    }

    #[DataProvider('emptyQrBillMocksProvider')]
    public function testEmptyQrBillValuesAreValid(bool $createCreditorInformationMock, bool $createPaymentReferenceMock)
    {
        $qrBillMock = $this->getQrBillMock(
            ($createCreditorInformationMock) ? $this->getCreditorInformationMock() : null,
            ($createPaymentReferenceMock) ? $this->getPaymentReferenceMock() : null,
        );

        $this->validator->validate($qrBillMock, new ValidCreditorInformationPaymentReferenceCombination());

        $this->assertNoViolation();
    }

    public static function emptyQrBillMocksProvider(): array
    {
        return [
         // createCreditorInformationMock, createPaymentReferenceMock
            [false, false,],
            [true, false,],
            [false, true,],
        ];
    }

    #[DataProvider('validCombinationsQrBillMocksProvider')]
    public function testValidCombinations(bool $containsQrIban, string $paymentReferenceType)
    {
        $qrBillMock = $this->getQrBillMock(
            $this->getCreditorInformationMock('any-iban', $containsQrIban),
            $this->getPaymentReferenceMock($paymentReferenceType)
        );

        $this->validator->validate($qrBillMock, new ValidCreditorInformationPaymentReferenceCombination());

        $this->assertNoViolation();
    }

    public static function validCombinationsQrBillMocksProvider(): array
    {
        return [
         // containsQrIban, paymentReferenceType
            [true, PaymentReference::TYPE_QR,],
            [false, PaymentReference::TYPE_SCOR,],
            [false, PaymentReference::TYPE_NON,],
        ];
    }

    #[DataProvider('invalidCombinationsQrBillMocksProvider')]
    public function testInvalidCombinations(bool $containsQrIban, string $paymentReferenceType)
    {
        $qrBillMock = $this->getQrBillMock(
            $this->getCreditorInformationMock('any-iban', $containsQrIban),
            $this->getPaymentReferenceMock($paymentReferenceType)
        );

        $this->validator->validate($qrBillMock, new ValidCreditorInformationPaymentReferenceCombination([
            'message' => 'myMessage',
        ]));

        $this->buildViolation('myMessage')
            ->setParameter('{{ referenceType }}', $qrBillMock->getPaymentReference()->getType())
            ->setParameter('{{ iban }}', $qrBillMock->getCreditorInformation()->getIban())
            ->assertRaised();
    }

    public static function invalidCombinationsQrBillMocksProvider(): array
    {
        return [
        // containsQrIban, paymentReferenceType
          [false, PaymentReference::TYPE_QR,],
          [true, PaymentReference::TYPE_SCOR,],
          [true, PaymentReference::TYPE_NON,],
        ];
    }

    public function getQrBillMock(?CreditorInformation $creditorInformation = null, ?PaymentReference $paymentReference = null)
    {
        $qrBill = $this->createMock(QrBill::class);

        $qrBill->method('getCreditorInformation')
            ->willReturn($creditorInformation);

        $qrBill->method('getPaymentReference')
            ->willReturn($paymentReference);

        return $qrBill;
    }

    public function getCreditorInformationMock(string $iban = '', bool $containsQrIban = false)
    {
        $creditorInformation = $this->createMock(CreditorInformation::class);

        $creditorInformation->method('getIban')
            ->willReturn($iban);

        $creditorInformation->method('containsQrIban')
            ->willReturn($containsQrIban);

        return $creditorInformation;
    }

    public function getPaymentReferenceMock(string $paymentReferenceType = '')
    {
        $paymentReference = $this->createMock(PaymentReference::class);

        $paymentReference->method('getType')
            ->willReturn($paymentReferenceType);

        return $paymentReference;
    }
}
