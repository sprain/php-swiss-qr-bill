<?php

namespace Sprain\SwissQrBill\DataGroups;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class PaymentAmountInformation
{
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

    /**
     * Due date on which, according to the biller, the payment should be paid at the latest
     *
     * @var \DateTime
     */
    private $dueDate;


    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount = null)
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency)
    {
        $this->currency = strtoupper($currency);

        return $this;
    }

    public function getDueDate(): \DateTime
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTime $dueDate = null)
    {
        $this->dueDate = $dueDate;

        return $this;
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