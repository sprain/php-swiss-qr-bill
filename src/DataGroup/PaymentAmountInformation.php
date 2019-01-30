<?php

namespace Sprain\SwissQrBill\DataGroup;

use Sprain\SwissQrBill\DataGroup\Interfaces\QrCodeData;
use Sprain\SwissQrBill\Validator\Interfaces\SelfValidatable;
use Sprain\SwissQrBill\Validator\ValidatorTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class PaymentAmountInformation implements QrCodeData, SelfValidatable
{
    use ValidatorTrait;

    const CURRENCY_CHF = 'CHF';
    const CURRENCY_EUR = 'EUR';

    /**
     * The payment amount due
     *
     * @var float
     */
    private $amount;

    /**
     * Payment currency code (ISO 4217)
     *
     * @var string
     */
    private $currency;


    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount = null) : self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency)
    {
        $this->currency = strtoupper($currency);

        return $this;
    }

    public function getQrCodeData() : array
    {
        return [
            $this->getAmount() ? number_format($this->getAmount(), 2, '.', '') : null,
            $this->getCurrency()
        ];
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraints('amount', [
            new Assert\Range([
                'min' => 0,
                'max'=> 999999999.99
            ]),
        ]);

        $metadata->addPropertyConstraints('currency', [
            new Assert\Choice([
                self::CURRENCY_CHF,
                self::CURRENCY_EUR
            ])
        ]);
    }
}