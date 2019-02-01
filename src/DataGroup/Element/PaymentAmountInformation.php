<?php

namespace Sprain\SwissQrBill\DataGroup\Element;

use Sprain\SwissQrBill\DataGroup\QrCodeableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class PaymentAmountInformation implements QrCodeableInterface, SelfValidatableInterface
{
    use SelfValidatableTrait;

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

    public static function create(string $currency, ?float $amount = null): self
    {
        $paymentInformation = new self();
        $paymentInformation->currency = strtoupper($currency);
        $paymentInformation->amount = $amount;

        return $paymentInformation;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function getQrCodeData(): array
    {
        return [
            $this->getAmount() ? number_format($this->getAmount(), 2, '.', ''): null,
            $this->getCurrency()
        ];
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
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