<?php

namespace Sprain\SwissQrBill\Tests\Constraints;

use Sprain\SwissQrBill\Constraints\ValidCreditorReference;
use Sprain\SwissQrBill\Constraints\ValidCreditorReferenceValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ValidCreditorReferenceTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
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

    /**
     * @dataProvider getValidCreditorReferences
     */
    public function testValidCreditorReferences($value)
    {
        $this->validator->validate($value, new ValidCreditorReference());

        $this->assertNoViolation();
    }

    public function getValidCreditorReferences()
    {
        return [
            ['RF45 1234 5123 45'],
            ['RF451234512345']
        ];
    }

    /**
     * @dataProvider getInvalidCreditorReferences
     */
    public function testInvalidCreditorReferences($creditorReference)
    {
        $constraint = new ValidCreditorReference(array(
            'message' => 'myMessage',
        ));

        $this->validator->validate($creditorReference, $constraint);

        $this->buildViolation('myMessage')
            ->setParameter('{{ string }}', $creditorReference)
            ->assertRaised();
    }

    public function getInvalidCreditorReferences()
    {
        return [
            ['RF43 1234 5123 45'],
            ['RF431234512345'],
            ['foo']
        ];
    }
}
