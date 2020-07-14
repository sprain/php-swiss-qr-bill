<?php

namespace Sprain\SwissQrBill\Reference;

use kmukku\phpIso11649\phpIso11649;
use Sprain\SwissQrBill\Validator\Exception\InvalidCreditorReferenceException;
use Sprain\SwissQrBill\Validator\SelfValidatableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class RfCreditorReferenceGenerator implements SelfValidatableInterface
{
    use SelfValidatableTrait;

    /**
     * @var string
     */
    protected $reference;

    /**
     * Transform a string to a valid CreditorReference.
     *
     * @param string $reference
     * @return string
     * @throws \Exception
     */
    public static function generate(string $reference) : string
    {
        $generator = new self($reference);

        return $generator->doGenerate();
    }

    /**
     * RfCreditorReferenceGenerator constructor.
     *
     * @param $reference
     */
    public function __construct(string $reference)
    {
        $this->reference = str_replace(' ', '', $reference);
    }

    /**
     * Run the generator.
     *
     * @return string
     * @throws \Exception
     */
    public function doGenerate() : string
    {
        if (!$this->isValid()) {
            throw new InvalidCreditorReferenceException(
                'The provided data is not valid to generate a creditor reference. Use getViolations() to find details.'
            );
        }

        $generator = new phpIso11649();

        return $generator->generateRfReference($this->reference, false);
    }

    /**
     * {@inheritDoc}
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraints('reference', [
            new Assert\Regex([
                'pattern' => '~^[a-zA-Z0-9]*$~',
                'match' => true
            ]),
            new Assert\Length([
                'min' => 1,
                'max' => 21 // 25 - 'RF' - CheckSum
            ])
        ]);
    }
}