<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\Constraint;

use Symfony\Component\Validator\Constraint;

class ValidCreditorInformationPaymentReferenceCombination extends Constraint
{
    public $message = 'The payment reference type "{{ referenceType }}" does not match with the iban type of "{{ iban }}".';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
