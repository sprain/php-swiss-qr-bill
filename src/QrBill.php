<?php declare(strict_types=1);

namespace Sprain\SwissQrBill;

use Endroid\QrCode\ErrorCorrectionLevel;
use Sprain\SwissQrBill\Constraint\ValidCreditorInformationPaymentReferenceCombination;
use Sprain\SwissQrBill\DataGroup\AddressInterface;
use Sprain\SwissQrBill\DataGroup\Element\AdditionalInformation;
use Sprain\SwissQrBill\DataGroup\Element\AlternativeScheme;
use Sprain\SwissQrBill\DataGroup\Element\CreditorInformation;
use Sprain\SwissQrBill\DataGroup\Element\Header;
use Sprain\SwissQrBill\DataGroup\Element\PaymentAmountInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentReference;
use Sprain\SwissQrBill\DataGroup\EmptyElement\EmptyAdditionalInformation;
use Sprain\SwissQrBill\DataGroup\EmptyElement\EmptyAddress;
use Sprain\SwissQrBill\DataGroup\QrCodeableInterface;
use Sprain\SwissQrBill\Exception\InvalidQrBillDataException;
use Sprain\SwissQrBill\QrCode\QrCode;
use Sprain\SwissQrBill\String\StringModifier;
use Sprain\SwissQrBill\Validator\SelfValidatableInterface;
use Sprain\SwissQrBill\Validator\SelfValidatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

final class QrBill implements SelfValidatableInterface
{
    use SelfValidatableTrait;

    private const SWISS_CROSS_LOGO_FILE = __DIR__ . '/../assets/swiss-cross.optimized.png';
    private const PX_QR_CODE = 543;    // recommended 46x46 mm in px @ 300dpi – in pixel based outputs, the final image size may be slightly different, depending on the qr code contents
    private const PX_SWISS_CROSS = 83; // recommended 7x7 mm in px @ 300dpi

    private Header $header;
    private ?CreditorInformation $creditorInformation = null;
    private ?AddressInterface $creditor = null;
    private ?PaymentAmountInformation $paymentAmountInformation = null;
    private ?AddressInterface $ultimateDebtor = null;
    private ?PaymentReference $paymentReference = null;
    private ?AdditionalInformation $additionalInformation = null;
    /** @var AlternativeScheme[] */
    private array $alternativeSchemes = [];

    private function __construct(Header $header)
    {
        $this->header = $header;
    }

    public static function create(): self
    {
        $header = Header::create(
            Header::QRTYPE_SPC,
            Header::VERSION_0200,
            Header::CODING_LATIN
        );

        return new self($header);
    }

    public function getHeader(): Header
    {
        return $this->header;
    }

    public function setHeader(Header $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function getCreditorInformation(): ?CreditorInformation
    {
        return $this->creditorInformation;
    }

    public function setCreditorInformation(CreditorInformation $creditorInformation): self
    {
        $this->creditorInformation = $creditorInformation;

        return $this;
    }

    public function getCreditor(): ?AddressInterface
    {
        return $this->creditor;
    }

    public function setCreditor(AddressInterface $creditor): self
    {
        $this->creditor = $creditor;
        
        return $this;
    }

    public function getPaymentAmountInformation(): ?PaymentAmountInformation
    {
        return $this->paymentAmountInformation;
    }

    public function setPaymentAmountInformation(PaymentAmountInformation $paymentAmountInformation): self
    {
        $this->paymentAmountInformation = $paymentAmountInformation;
        
        return $this;
    }

    public function getUltimateDebtor(): ?AddressInterface
    {
        return $this->ultimateDebtor;
    }

    public function setUltimateDebtor(AddressInterface $ultimateDebtor): self
    {
        $this->ultimateDebtor = $ultimateDebtor;
        
        return $this;
    }

    public function getPaymentReference(): ?PaymentReference
    {
        return $this->paymentReference;
    }

    public function setPaymentReference(PaymentReference $paymentReference): self
    {
        $this->paymentReference = $paymentReference;
        
        return $this;
    }

    public function getAdditionalInformation(): ?AdditionalInformation
    {
        return $this->additionalInformation;
    }

    public function setAdditionalInformation(AdditionalInformation $additionalInformation): self
    {
        $this->additionalInformation = $additionalInformation;

        return $this;
    }

    public function getAlternativeSchemes(): array
    {
        return $this->alternativeSchemes;
    }

    public function setAlternativeSchemes(array $alternativeSchemes): self
    {
        $this->alternativeSchemes = $alternativeSchemes;

        return $this;
    }

    public function addAlternativeScheme(AlternativeScheme $alternativeScheme): self
    {
        $this->alternativeSchemes[] = $alternativeScheme;

        return $this;
    }

    /**
     * @throws InvalidQrBillDataException
     */
    public function getQrCode(): QrCode
    {
        if (!$this->isValid()) {
            throw new InvalidQrBillDataException(
                'The provided data is not valid to generate a qr code. Use getViolations() to find details.'
            );
        }

        $qrCode = new QrCode();
        $qrCode->setText($this->getQrCodeContent());
        $qrCode->setSize(self::PX_QR_CODE);
        $qrCode->setLogoHeight(self::PX_SWISS_CROSS);
        $qrCode->setLogoWidth(self::PX_SWISS_CROSS);
        $qrCode->setLogoPath(self::SWISS_CROSS_LOGO_FILE);
        $qrCode->setRoundBlockSize(true, \Endroid\QrCode\QrCode::ROUND_BLOCK_SIZE_MODE_ENLARGE);
        $qrCode->setMargin(0);
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::MEDIUM));

        return $qrCode;
    }

    private function getQrCodeContent(): string
    {
        $elements = [
            $this->getHeader(),
            $this->getCreditorInformation(),
            $this->getCreditor(),
            new EmptyAddress(), # Placeholder for ultimateCreditor, which is currently not yet allowed to be used by the implementation guidelines
            $this->getPaymentAmountInformation(),
            $this->getUltimateDebtor() ?: new EmptyAddress(),
            $this->getPaymentReference(),
            $this->getAdditionalInformation() ?: new EmptyAdditionalInformation(),
            $this->getAlternativeSchemes()
        ];

        $qrCodeStringElements = $this->extractQrCodeDataFromElements($elements);

        return implode("\n", $qrCodeStringElements);
    }

    private function extractQrCodeDataFromElements(array $elements): array
    {
        $qrCodeElements = [];

        foreach ($elements as $element) {
            if ($element instanceof QrCodeableInterface) {
                $qrCodeElements = array_merge($qrCodeElements, $element->getQrCodeData());
            } elseif (is_array($element)) {
                $qrCodeElements = array_merge($qrCodeElements, $this->extractQrCodeDataFromElements($element));
            }
        }

        array_walk($qrCodeElements, function (&$string) {
            if (is_string($string)) {
                $string = StringModifier::replaceLineBreaksWithString($string);
                $string = StringModifier::replaceMultipleSpacesWithOne($string);
                $string = trim($string);
            }
        });

        return $qrCodeElements;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addConstraint(
            new ValidCreditorInformationPaymentReferenceCombination()
        );

        $metadata->addPropertyConstraints('header', [
            new Assert\NotNull(),
            new Assert\Valid()
        ]);

        $metadata->addPropertyConstraints('creditorInformation', [
            new Assert\NotNull(),
            new Assert\Valid()
        ]);

        $metadata->addPropertyConstraints('creditor', [
            new Assert\NotNull(),
            new Assert\Valid()
        ]);

        $metadata->addPropertyConstraints('paymentAmountInformation', [
            new Assert\NotNull(),
            new Assert\Valid()
        ]);

        $metadata->addPropertyConstraints('ultimateDebtor', [
            new Assert\Valid()
        ]);

        $metadata->addPropertyConstraints('paymentReference', [
            new Assert\NotNull(),
            new Assert\Valid()
        ]);

        $metadata->addPropertyConstraints('alternativeSchemes', [
            new Assert\Count([
                'max' => 2
            ]),
            new Assert\Valid([
                'traverse' => true
            ])
        ]);
    }
}
