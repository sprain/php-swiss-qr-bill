<?php

namespace Sprain\SwissQrBill\DataGroup\Element;

use Sprain\SwissQrBill\DataGroup\QrCodeableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadataInterface;

class AlternativeScheme implements QrCodeableInterface, SelfValidatableInterface
{
    use SelfValidatableTrait;

    /**
     * Parameter character chain of the alternative scheme
     * 
     * @var string
     */
    private $parameter;

    public function getParameter(): ?string
    {
        return $this->parameter;
    }

    public function setParameter(string $parameter) : self
    {
        $this->parameter = $parameter;

        return $this;
    }

    public function getQrCodeData() : array
    {
        return [
            $this->getParameter()
        ];
    }

    /**
     * Note that no real-life alternative schemes yet exist. Therefore validation is kept simple yet.
     * @link https://www.paymentstandards.ch/en/home/software-partner/alternative-schemes.html
     */
    public static function loadValidatorMetadata(ClassMetadataInterface $metadata) : void
    {
        $metadata->addPropertyConstraints('parameter', [
            new Assert\NotBlank(),
            new Assert\Length([
                'max' => 100
            ])
        ]);
    }
}