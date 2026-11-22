<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @internal
 */
final class ValidCreditorInformationPaymentReferenceCombination extends Constraint
{
    public string $message = 'The payment reference type "{{ referenceType }}" does not match with the iban type of "{{ iban }}".';

    public string $currencyMessage = 'Neither a QR-IBAN nor a payment reference of type "QRR" is allowed for currency "{{ currency }}". Use a regular IBAN with a reference of type "SCOR" or "NON" instead.';

    #[\Override]
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
