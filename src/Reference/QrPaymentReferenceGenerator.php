<?php

namespace Sprain\SwissQrBill\Reference;

use Sprain\SwissQrBill\Validator\Exception\InvalidQrPaymentReferenceException;
use Sprain\SwissQrBill\Validator\SelfValidatableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadataInterface;

class QrPaymentReferenceGenerator implements SelfValidatableInterface
{
    use SelfValidatableTrait;

    /** @var string */
    private $customerIdentificationNumber;

    /** @var string */
    private $referenceNumber;

    public static function generate(string $customerIdentificationNumber, string $referenceNumber)
    {
        $qrPaymentReferenceGenerator = new self();
        $qrPaymentReferenceGenerator->customerIdentificationNumber = $qrPaymentReferenceGenerator->removeWhitespace($customerIdentificationNumber);
        $qrPaymentReferenceGenerator->referenceNumber = $qrPaymentReferenceGenerator->removeWhitespace($referenceNumber);

        return $qrPaymentReferenceGenerator->doGenerate();
    }

    public function getCustomerIdentificationNumber() : ?string
    {
        return $this->customerIdentificationNumber;
    }

    public function getReferenceNumber() : ?string
    {
        return $this->referenceNumber;
    }

    public function doGenerate()
    {
        if (!$this->isValid()) {
            throw new InvalidQrPaymentReferenceException(
                'The provided data is not valid to generate a qr payment reference number. Use getViolations() to find details.'
            );
        }

        $completeReferenceNumber  = str_pad($this->getCustomerIdentificationNumber(), 6, '0', STR_PAD_RIGHT);
        $completeReferenceNumber .= str_pad($this->getReferenceNumber(), 20, '0', STR_PAD_LEFT);
        $completeReferenceNumber .= $this->modulo10($completeReferenceNumber);

        return $completeReferenceNumber;
    }

    public static function loadValidatorMetadata(ClassMetadataInterface $metadata) : void
    {
        $metadata->addPropertyConstraints('customerIdentificationNumber', [
            // Only numbers are allowed (including leading zeros)
            new Assert\Regex([
                'pattern' => '/^\d*$/',
                'match' => true
            ]),
            new Assert\Length([
                'max' => 6
            ]),
            new Assert\NotBlank()
        ]);

        $metadata->addPropertyConstraints('referenceNumber', [
            // Only numbers are allowed (including leading zeros)
            new Assert\Regex([
                'pattern' => '/^\d*$/',
                'match' => true
            ]),
            new Assert\Length([
                'max' => 20
            ]),
            new Assert\NotBlank()
        ]);
    }

    private function removeWhitespace(string $string) : string
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
