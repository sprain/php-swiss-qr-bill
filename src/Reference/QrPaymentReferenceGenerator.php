<?php

namespace Sprain\SwissQrBill\Reference;

use Sprain\SwissQrBill\Validator\Exception\InvalidQrPaymentReferenceException;
use Sprain\SwissQrBill\Validator\SelfValidatableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class QrPaymentReferenceGenerator implements SelfValidatableInterface
{
    use SelfValidatableTrait;

    /** @var string */
    private $customerIdentificationNumber;

    /** @var string */
    private $referenceNumber;

    public static function generate(?string $customerIdentificationNumber, string $referenceNumber)
    {
        $qrPaymentReferenceGenerator = new self();

        if (null !== $customerIdentificationNumber) {
            $qrPaymentReferenceGenerator->customerIdentificationNumber = $qrPaymentReferenceGenerator->removeWhitespace($customerIdentificationNumber);
        }
        $qrPaymentReferenceGenerator->referenceNumber = $qrPaymentReferenceGenerator->removeWhitespace($referenceNumber);

        return $qrPaymentReferenceGenerator->doGenerate();
    }

    public function getCustomerIdentificationNumber(): ?string
    {
        return $this->customerIdentificationNumber;
    }

    public function getReferenceNumber(): ?string
    {
        return $this->referenceNumber;
    }

    private function doGenerate()
    {
        if (!$this->isValid()) {
            throw new InvalidQrPaymentReferenceException(
                'The provided data is not valid to generate a qr payment reference number. Use getViolations() to find details.'
            );
        }

        $completeReferenceNumber  = $this->getCustomerIdentificationNumber();
        $completeReferenceNumber .= str_pad($this->getReferenceNumber(), 26 - strlen($completeReferenceNumber), '0', STR_PAD_LEFT);
        $completeReferenceNumber .= $this->modulo10($completeReferenceNumber);

        return $completeReferenceNumber;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraints('customerIdentificationNumber', [
            // Only numbers are allowed (including leading zeros)
            new Assert\Regex([
                'pattern' => '/^\d*$/',
                'match' => true
            ]),
            new Assert\Length([
                'max' => 11
            ]),
        ]);

        $metadata->addPropertyConstraints('referenceNumber', [
            // Only numbers are allowed (including leading zeros)
            new Assert\Regex([
                'pattern' => '/^\d*$/',
                'match' => true
            ]),
            new Assert\NotBlank()
        ]);

        $metadata->addConstraint(new Assert\Callback('validateFullReference'));
    }

    public function validateFullReference(ExecutionContextInterface $context, $payload)
    {
        if (strlen($this->customerIdentificationNumber) + strlen($this->referenceNumber) > 26) {
            $context->buildViolation('The length of customer identification number + reference number may not exceed 26 characters in total.')
                ->addViolation();
        }
    }

    private function removeWhitespace(string $string): string
    {
        return preg_replace('/\s+/', '', $string);
    }

    private function modulo10($number)
    {
        $table = array(0, 9, 4, 6, 8, 2, 7, 1, 3, 5);
        $next = 0;
        for ($i = 0; $i < strlen($number); $i++) {
            $next =  $table[($next + substr($number, $i, 1)) % 10];
        }

        return (10 - $next) % 10;
    }
}
