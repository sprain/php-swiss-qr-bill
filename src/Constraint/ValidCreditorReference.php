<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\Constraint;

use Symfony\Component\Validator\Constraint;

final class ValidCreditorReference extends Constraint
{
    public string $message = 'The string "{{ string }}" is not a valid Creditor Reference.';
}
