<?php

namespace Sprain\SwissQrBill\Constraint;

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

        // Catch any invalid characters which are not allowed in ISO11649
        // (but may not be caught by the underlying library)
        if (!preg_match('/^[\w ]*$/', $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();

            return;
        }

        $referenceGenerator = new phpIso11649();

        if (false === $referenceGenerator->validateRfReference($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}