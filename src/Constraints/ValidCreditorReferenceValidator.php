<?php

namespace Sprain\SwissQrBill\Constraints;

use kmukku\phpIso11649\phpIso11649;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidCreditorReferenceValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        $referenceGenerator = new phpIso11649();

        if (false == $referenceGenerator->validateRfReference($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}