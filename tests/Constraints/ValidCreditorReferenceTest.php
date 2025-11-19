<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill\Constraints;

use PHPUnit\Framework\Attributes\DataProvider;
use Sprain\SwissQrBill\Constraint\ValidCreditorReference;
use Sprain\SwissQrBill\Constraint\ValidCreditorReferenceValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

final class ValidCreditorReferenceTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new ValidCreditorReferenceValidator();
    }

    public function testNullIsValid()
    {
        $this->validator->validate(null, new ValidCreditorReference());

        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid()
    {
        $this->validator->validate('', new ValidCreditorReference());

        $this->assertNoViolation();
    }

    #[DataProvider('getValidCreditorReferences')]
    public function testValidCreditorReferences($value)
    {
        $this->validator->validate($value, new ValidCreditorReference());

        $this->assertNoViolation();
    }

    public static function getValidCreditorReferences()
    {
        return [
            ['RF45 1234 5123 45'],
            ['RF451234512345']
        ];
    }

    #[DataProvider('getInvalidCreditorReferences')]
    public function testInvalidCreditorReferences($creditorReference)
    {
        $constraint = new ValidCreditorReference();
        $this->validator->validate($creditorReference, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ string }}', $creditorReference)
            ->assertRaised();
    }

    public static function getInvalidCreditorReferences()
    {
        return [
            ['RF43 1234 5123 45'],
            ['RF431234512345'],
            ['RF431234512345Ä'],
            ['foo']
        ];
    }
}
