<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\DataGroup\Element;

use Sprain\SwissQrBill\DataGroup\QrCodeableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

final class PaymentAmountInformation implements QrCodeableInterface, SelfValidatableInterface
{
    use SelfValidatableTrait;

    public const CURRENCY_CHF = 'CHF';
    public const CURRENCY_EUR = 'EUR';

    private function __construct(
        /**
         * Payment currency code (ISO 4217)
         */
        private string $currency,

        /**
         * The payment amount due
         */
        private readonly ?float $amount
    ) {
        $this->currency = strtoupper($currency);
    }

    public static function create(string $currency, ?float $amount = null): self
    {
        return new self($currency, $amount);
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function getFormattedAmount(): ?string
    {
        if (null === $this->amount) {
            return '';
        }

        return number_format(
            $this->amount,
            2,
            '.',
            ' '
        );
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getQrCodeData(): array
    {
        return [
            $this->getFormattedAmountForQrCode(),
            $this->getCurrency()
        ];
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraints('amount', [
            new Assert\Range(
                min: 0,
                max: 999999999.99
            ),
        ]);

        $metadata->addPropertyConstraints('currency', [
            new Assert\Choice(choices: [
                self::CURRENCY_CHF,
                self::CURRENCY_EUR
            ])
        ]);

        $metadata->addConstraint(new Assert\Callback('validateAmountDecimals'));
    }

    public function validateAmountDecimals(ExecutionContextInterface $context): void
    {
        if (null === $this->amount) {
            return;
        }

        if (abs((float) $this->amount - round((float) $this->amount, 2)) > 0) {
            $context->buildViolation('The amount must not have more than 2 decimal places.')
                ->atPath('amount')
                ->addViolation();
        }
    }

    private function getFormattedAmountForQrCode(): ?string
    {
        if (null === $this->amount) {
            return null;
        }

        // Unlike the formatted code for the payment part, the amount in the qr code has no thousands separator
        return number_format($this->amount, 2, '.', '');
    }
}
