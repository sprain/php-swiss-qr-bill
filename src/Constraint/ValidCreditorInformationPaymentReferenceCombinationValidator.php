<?php

namespace Sprain\SwissQrBill\Constraint;

use kmukku\phpIso11649\phpIso11649;
use Sprain\SwissQrBill\DataGroup\Element\PaymentReference;
use Sprain\SwissQrBill\QrBill;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidCreditorInformationPaymentReferenceCombinationValidator extends ConstraintValidator
{
    public function validate($qrBill, Constraint $constraint)
    {
        if (!$qrBill instanceof QrBill) {
            return;
        }

        $creditorInformation = $qrBill->getCreditorInformation();
        $paymentReference = $qrBill->getPaymentReference();

        if (null === $creditorInformation || null === $paymentReference) {
            return;
        }

        if (null === $creditorInformation->getIban() || null === $paymentReference->getType()) {
            return;
        }

        if ($creditorInformation->containsQrIban()) {
            if ($qrBill->getPaymentReference()->getType() !== PaymentReference::TYPE_QR) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ referenceType }}', $paymentReference->getType())
                    ->setParameter('{{ iban }}', $creditorInformation->getIban())
                    ->addViolation();
            }
        } else {
            if ($qrBill->getPaymentReference()->getType() === PaymentReference::TYPE_QR) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ referenceType }}', $paymentReference->getType())
                    ->setParameter('{{ iban }}', $creditorInformation->getIban())
                    ->addViolation();
            }
        }
    }
}