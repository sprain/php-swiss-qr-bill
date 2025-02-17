<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\DataGroup\Element;

use Sprain\SwissQrBill\Constraint\ValidCreditorReference;
use Sprain\SwissQrBill\DataGroup\QrCodeableInterface;
use Sprain\SwissQrBill\String\StringModifier;
use Sprain\SwissQrBill\Validator\SelfValidatableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\GroupSequenceProviderInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

final class PaymentReference implements GroupSequenceProviderInterface, QrCodeableInterface, SelfValidatableInterface
{
    use SelfValidatableTrait;

    public const TYPE_QR = 'QRR';
    public const TYPE_SCOR = 'SCOR';
    public const TYPE_NON = 'NON';

    private function __construct(
        /**
         * Reference type
         */
        private readonly string $type,

        /**
         * Structured reference number
         * Either a QR reference or a Creditor Reference (ISO 11649)
         */
        private ?string $reference
    ) {
        $this->handleWhiteSpaceInReference();
    }

    public static function create(string $type, ?string $reference = null): self
    {
        return new self($type, $reference);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function getFormattedReference(): ?string
    {
        return match ($this->type) {
            self::TYPE_QR => trim(strrev(chunk_split(strrev((string) $this->reference), 5, ' '))),
            self::TYPE_SCOR => trim(chunk_split((string) $this->reference, 4, ' ')),
            default => null,
        };
    }

    public function getQrCodeData(): array
    {
        return [
            $this->getType(),
            $this->getReference()
        ];
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->setGroupSequenceProvider(true);

        $metadata->addPropertyConstraints('type', [
            new Assert\NotBlank([
                'groups' => ['default']
            ]),
            new Assert\Choice([
                'groups' => ['default'],
                'choices' => [
                    self::TYPE_QR,
                    self::TYPE_SCOR,
                    self::TYPE_NON
                ]
            ])
        ]);

        $metadata->addPropertyConstraints('reference', [
            /** @phpstan-ignore-next-line because docs do not match bc compatible syntax */
            new Assert\Type([
                'type' => 'alnum',
                'groups' => [self::TYPE_QR]
            ]),
            new Assert\NotBlank([
                'groups' => [self::TYPE_QR, self::TYPE_SCOR]
            ]),
            new Assert\Length([
                'min' => 27,
                'max' => 27,
                'groups' => [self::TYPE_QR]
            ]),
            new Assert\Blank([
                'groups' => [self::TYPE_NON]
            ]),
            new ValidCreditorReference([
                'groups' => [self::TYPE_SCOR]
            ])
        ]);
    }

    public function getGroupSequence(): array|GroupSequence
    {
        return [
            'default',
            $this->getType()
        ];
    }

    private function handleWhiteSpaceInReference(): void
    {
        if (null === $this->reference) {
            return;
        }

        $this->reference = StringModifier::stripWhitespace($this->reference);

        if ('' === $this->reference) {
            $this->reference = null;
        }
    }
}
