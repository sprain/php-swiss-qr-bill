<?php

namespace Sprain\SwissQrBill\Constraint;

use kmukku\phpIso11649\phpIso11649;
use Sprain\SwissQrBill\DataGroup\Element\PaymentReference;
use Sprain\SwissQrBill\QrBill;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidCreditorInformationPaymentReferenceCombinationValidator extends ConstraintValidator
{
    private const QR_IBAN_IS_ALLOWED = [
        PaymentReference::TYPE_QR   => true,
        PaymentReference::TYPE_SCOR => false,
        PaymentReference::TYPE_NON  => false,
    ];

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

        if (self::QR_IBAN_IS_ALLOWED[$paymentReference->getType()] !== $creditorInformation->containsQrIban()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ referenceType }}', $paymentReference->getType())
                ->setParameter('{{ iban }}', $creditorInformation->getIban())
                ->addViolation();
        }
    }
}