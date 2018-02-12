<?php

namespace Sprain\SwissQrBill\DataGroups;

use Sprain\SwissQrBill\Constraints\ValidCreditorReference;
use Sprain\SwissQrBill\DataGroups\Interfaces\QrCodeData;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class PaymentReference implements GroupSequenceProviderInterface, QrCodeData
{
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
     * Strutured reference number
     * Either a QR reference or a Creditor Reference (ISO 11649)
     *
     * @var string
     */
    private $reference;

    /**
     * Unstructured message
     *
     * @var string
     */
    private $message;


    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type) : self
    {
        $this->type = $type;

        return $this;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference) : self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message) : self
    {
        $this->message = $message;

        return $this;
    }

    public function getQrCodeData() : array
    {
        return [
            $this->getType(),
            $this->getReference(),
            $this->getMessage(),
        ];
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->setGroupSequenceProvider(true);

        $metadata->addPropertyConstraints('type', [
            new Assert\NotBlank(),
            new Assert\Choice([
                self::TYPE_QR,
                self::TYPE_SCOR,
                self::TYPE_NON
            ])
        ]);

        $metadata->addPropertyConstraints('reference', [
            new Assert\Type([
                'type' => 'alnum',
                'groups' => [self::TYPE_QR]
            ]),
            new Assert\NotBlank([
                'groups' => [self::TYPE_QR]
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

        $metadata->addPropertyConstraints('message', [
            new Assert\Length([
                'max'=> 140
            ])
        ]);
    }

    public function getGroupSequence()
    {
        $groups = array('Default');
        $groups[] = $this->getType();

        return $groups;
    }
}