<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\Reference;

use Sprain\SwissQrBill\String\StringModifier;
use Sprain\SwissQrBill\Validator\Exception\InvalidQrPaymentReferenceException;
use Sprain\SwissQrBill\Validator\SelfValidatableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

final class QrPaymentReferenceGenerator implements SelfValidatableInterface
{
    use SelfValidatableTrait;

    public static function generate(?string $customerIdentificationNumber, string $referenceNumber): string
    {
        $qrPaymentReferenceGenerator = new self($customerIdentificationNumber, $referenceNumber);

        return $qrPaymentReferenceGenerator->doGenerate();
    }

    private function __construct(
        private ?string $customerIdentificationNumber,
        private string $referenceNumber
    ) {
        if (null !== $customerIdentificationNumber) {
            $this->customerIdentificationNumber = StringModifier::stripWhitespace($customerIdentificationNumber);
        }
        $this->referenceNumber = StringModifier::stripWhitespace($referenceNumber);
    }

    private function doGenerate(): string
    {
        if (!$this->isValid()) {
            throw new InvalidQrPaymentReferenceException(
                'The provided data is not valid to generate a qr payment reference number.'
            );
        }

        $completeReferenceNumber  = $this->customerIdentificationNumber;

        $strlen = $completeReferenceNumber ? strlen($completeReferenceNumber) : 0;
        $completeReferenceNumber .= str_pad($this->referenceNumber, 26 - $strlen, '0', STR_PAD_LEFT);
        $completeReferenceNumber .= $this->modulo10($completeReferenceNumber);

        return $completeReferenceNumber;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraints('customerIdentificationNumber', [
            // Only numbers are allowed (including leading zeros)
            new Assert\Regex(
                pattern: '/^\d*$/',
                match: true
            ),
            new Assert\Length(
                max: 11
            ),
        ]);

        $metadata->addPropertyConstraints('referenceNumber', [
            new Assert\Regex(
                pattern: '/^\d*$/',
                match: true,
                message: 'The reference number must not contain any non-numeric characters.'
            ),
            new Assert\NotBlank()
        ]);

        $metadata->addConstraint(new Assert\Callback('validateFullReferenceLength'));
        $metadata->addConstraint(new Assert\Callback('validateNotExclusivelyZeros'));
    }

    public function validateFullReferenceLength(ExecutionContextInterface $context): void
    {
        $strlenCustomerIdentificationNumber = $this->customerIdentificationNumber ? strlen($this->customerIdentificationNumber) : 0;

        if ($strlenCustomerIdentificationNumber + strlen($this->referenceNumber) > 26) {
            $context->buildViolation('The length of customer identification number + reference number must not exceed 26 characters in total.')
                ->addViolation();
        }
    }

    public function validateNotExclusivelyZeros(ExecutionContextInterface $context): void
    {
        $regex = '/^0*$/';

        if (preg_match($regex, ($this->customerIdentificationNumber ?? '0')) && preg_match($regex, $this->referenceNumber)) {
            $context->buildViolation('The qr reference must not consist exclusively of zeros.')
                ->addViolation();
        }
    }

    private function modulo10(string $number): int
    {
        $table = [0, 9, 4, 6, 8, 2, 7, 1, 3, 5];
        $next = 0;
        $strlen = strlen($number);
        for ($i = 0; $i < $strlen; $i++) {
            $next =  $table[($next + (int)$number[$i]) % 10];
        }

        return (10 - $next) % 10;
    }
}
