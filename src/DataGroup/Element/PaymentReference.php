<?php

namespace Sprain\SwissQrBill\DataGroup\Element;

use Sprain\SwissQrBill\Constraint\ValidCreditorReference;
use Sprain\SwissQrBill\DataGroup\QrCodeableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;
use Symfony\Component\Validator\Mapping\ClassMetadataInterface;

class PaymentReference implements GroupSequenceProviderInterface, QrCodeableInterface, SelfValidatableInterface
{
    use SelfValidatableTrait;

    const TYPE_QR = 'QRR';
    const TYPE_SCOR = 'SCOR';
    const TYPE_NON = 'NON';

    /**
     * Reference type
     *
     * @var string
     */
    private $type;

    /**
     * Structured reference number
     * Either a QR reference or a Creditor Reference (ISO 11649)
     *
     * @var string
     */
    private $reference;

    public static function create(string $type, ?string $reference = null) : self
    {
        $paymentReference = new self();
        $paymentReference->type = $type;
        $paymentReference->reference = $reference;

        return $paymentReference;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function getQrCodeData() : array
    {
        return [
            $this->getType(),
            $this->getReference()
        ];
    }

    public static function loadValidatorMetadata(ClassMetadataInterface $metadata) : void
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

    public function getGroupSequence()
    {
        $groups = [
            'default',
            $this->getType()
        ];

        return $groups;
    }
}