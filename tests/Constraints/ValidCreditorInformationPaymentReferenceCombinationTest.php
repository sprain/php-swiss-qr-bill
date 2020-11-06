<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill\Constraints;

use Sprain\SwissQrBill\Constraint\ValidCreditorInformationPaymentReferenceCombination;
use Sprain\SwissQrBill\Constraint\ValidCreditorInformationPaymentReferenceCombinationValidator;
use Sprain\SwissQrBill\DataGroup\Element\CreditorInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentReference;
use Sprain\SwissQrBill\QrBill;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ValidCreditorInformationPaymentReferenceCombinationTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
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

    /**
     * @dataProvider emptyQrBillMocksProvider
     */
    public function testEmptyQrBillValuesAreValid($qrBillMock)
    {
        $this->validator->validate($qrBillMock, new ValidCreditorInformationPaymentReferenceCombination());

        $this->assertNoViolation();
    }

    public function emptyQrBillMocksProvider()
    {
        return [
            [$this->getQrBillMock()],
            [$this->getQrBillMock(
                $this->getCreditorInformationMock(),
                $this->getPaymentReferenceMock()
            )],
            [$this->getQrBillMock(
                $this->getCreditorInformationMock(),
                null
            )],
            [$this->getQrBillMock(
                null,
                $this->getPaymentReferenceMock()
            )],
            [$this->getQrBillMock(
                $this->getCreditorInformationMock('any-iban'),
                $this->getPaymentReferenceMock()
            )],
            [$this->getQrBillMock(
                $this->getCreditorInformationMock(),
                $this->getPaymentReferenceMock(PaymentReference::TYPE_QR)
            )],
        ];
    }

    /**
     * @dataProvider validCombinationsQrBillMocksProvider
     */
    public function testValidCombinations($qrBillMock)
    {
        $this->validator->validate($qrBillMock, new ValidCreditorInformationPaymentReferenceCombination());

        $this->assertNoViolation();
    }

    public function validCombinationsQrBillMocksProvider()
    {
        return [
            [$this->getQrBillMock(
                $this->getCreditorInformationMock('any-iban', true),
                $this->getPaymentReferenceMock(PaymentReference::TYPE_QR)
            )],
            [$this->getQrBillMock(
                $this->getCreditorInformationMock('any-iban', false),
                $this->getPaymentReferenceMock(PaymentReference::TYPE_SCOR)
            )],
            [$this->getQrBillMock(
                $this->getCreditorInformationMock('any-iban', false),
                $this->getPaymentReferenceMock(PaymentReference::TYPE_NON)
            )],
        ];
    }

    /**
     * @dataProvider invalidCombinationsQrBillMocksProvider
     */
    public function testInvalidCombinations($qrBillMock)
    {
        $this->validator->validate($qrBillMock, new ValidCreditorInformationPaymentReferenceCombination([
            'message' => 'myMessage',
        ]));

        $this->buildViolation('myMessage')
            ->setParameter('{{ referenceType }}', $qrBillMock->getPaymentReference()->getType())
            ->setParameter('{{ iban }}', $qrBillMock->getCreditorInformation()->getIban())
            ->assertRaised();
    }

    public function invalidCombinationsQrBillMocksProvider()
    {
        return [
            [$this->getQrBillMock(
                $this->getCreditorInformationMock('any-iban', false),
                $this->getPaymentReferenceMock(PaymentReference::TYPE_QR)
            )],
            [$this->getQrBillMock(
                $this->getCreditorInformationMock('any-iban', true),
                $this->getPaymentReferenceMock(PaymentReference::TYPE_SCOR)
            )],
            [$this->getQrBillMock(
                $this->getCreditorInformationMock('any-iban', true),
                $this->getPaymentReferenceMock(PaymentReference::TYPE_NON)
            )],
        ];
    }

    public function getQrBillMock($creditorInformation = null, $paymentReference = null)
    {
        $qrBill = $this->createMock(QrBill::class);

        $qrBill->method('getCreditorInformation')
            ->willReturn($creditorInformation);

        $qrBill->method('getPaymentReference')
            ->willReturn($paymentReference);

        return $qrBill;
    }

    public function getCreditorInformationMock($iban = null, $containsQrIban = false)
    {
        $creditorInformation = $this->createMock(CreditorInformation::class);

        $creditorInformation->method('getIban')
            ->willReturn($iban);

        $creditorInformation->method('containsQrIban')
            ->willReturn($containsQrIban);

        return $creditorInformation;
    }

    public function getPaymentReferenceMock($paymentReferenceType = null)
    {
        $paymentReference = $this->createMock(PaymentReference::class);

        $paymentReference->method('getType')
            ->willReturn($paymentReferenceType);

        return $paymentReference;
    }
}
