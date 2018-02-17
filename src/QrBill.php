<?php

namespace Sprain\SwissQrBill;

use Endroid\QrCode\QrCode;
use Sprain\SwissQrBill\DataGroups\AlternativeScheme;
use Sprain\SwissQrBill\DataGroups\Creditor;
use Sprain\SwissQrBill\DataGroups\CreditorInformation;
use Sprain\SwissQrBill\DataGroups\Header;
use Sprain\SwissQrBill\DataGroups\Interfaces\QrCodeData;
use Sprain\SwissQrBill\DataGroups\PaymentAmountInformation;
use Sprain\SwissQrBill\DataGroups\PaymentReference;
use Sprain\SwissQrBill\DataGroups\UltimateCreditor;
use Sprain\SwissQrBill\DataGroups\UltimateDebtor;
use Sprain\SwissQrBill\Exception\InvalidQrBillDataException;
use Sprain\SwissQrBill\Validator\Interfaces\Validatable;
use Sprain\SwissQrBill\Validator\ValidatorTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class QrBill implements Validatable
{
    use ValidatorTrait;

    const SWISS_CROSS_LOGO_FILE = __DIR__ . '/../assets/swiss-cross.png';

    /** @var Header */
    private $header;

    /** @var CreditorInformation */
    private $creditorInformation;

    /** @var Creditor */
    private $creditor;

    /** @var UltimateCreditor */
    private $ultimateCreditor;

    /** @var PaymentAmountInformation */
    private $paymentAmountInformation;

    /** @var UltimateDebtor */
    private $ultimateDebtor;

    /** @var PaymentReference */
    private $paymentReference;

    /** @var AlternativeScheme[] */
    private $alternativeSchemes;

    public static function create() : self
    {
        $header = new Header();
        $header->setCoding(Header::CODING_LATIN);
        $header->setQrType(Header::QRTYPE_SPC);
        $header->setVersion(Header::VERSION_0100);

        $qrBill = new self();
        $qrBill->setHeader($header);

        return $qrBill;
    }

    public function __construct()
    {
        $this->alternativeSchemes = [];
    }

    public function getHeader(): Header
    {
        return $this->header;
    }

    public function setHeader(Header $header) : self
    {
        $this->header = $header;
        
        return $this;
    }

    public function getCreditorInformation(): CreditorInformation
    {
        return $this->creditorInformation;
    }

    public function setCreditorInformation(CreditorInformation $creditorInformation) : self
    {
        $this->creditorInformation = $creditorInformation;

        return $this;
    }

    public function getCreditor(): Creditor
    {
        return $this->creditor;
    }

    public function setCreditor(Creditor $creditor) : self
    {
        $this->creditor = $creditor;
        
        return $this;
    }

    public function getUltimateCreditor(): ?UltimateCreditor
    {
        return $this->ultimateCreditor;
    }

    public function setUltimateCreditor(UltimateCreditor $ultimateCreditor) : self
    {
        $this->ultimateCreditor = $ultimateCreditor;
        
        return $this;
    }

    public function getPaymentAmountInformation(): PaymentAmountInformation
    {
        return $this->paymentAmountInformation;
    }

    public function setPaymentAmountInformation(PaymentAmountInformation $paymentAmountInformation) : self
    {
        $this->paymentAmountInformation = $paymentAmountInformation;
        
        return $this;
    }

    public function getUltimateDebtor(): ?UltimateDebtor
    {
        return $this->ultimateDebtor;
    }

    public function setUltimateDebtor(UltimateDebtor $ultimateDebtor) : self
    {
        $this->ultimateDebtor = $ultimateDebtor;
        
        return $this;
    }

    public function getPaymentReference(): PaymentReference
    {
        return $this->paymentReference;
    }

    public function setPaymentReference(PaymentReference $paymentReference) : self
    {
        $this->paymentReference = $paymentReference;
        
        return $this;
    }

    public function getAlternativeSchemes(): array
    {
        return $this->alternativeSchemes;
    }

    public function setAlternativeSchemes(array $alternativeSchemes) : self
    {
        $this->alternativeSchemes = $alternativeSchemes;

        return $this;
    }

    public function getQrCode() : QrCode
    {
        $qrCode = new QrCode();
        $qrCode->setText($this->getQrCodeData());
        $qrCode->setSize(543); // recommended 46x46 mm in px @ 300dpi
        $qrCode->setMargin(19); // recommended 1,6 mm in px @ 300dpi
        $qrCode->setLogoPath(self::SWISS_CROSS_LOGO_FILE);
        $qrCode->setLogoWidth(83); // recommended 7x7 mm in px @ 300dpi

        return $qrCode;
    }

    private function getQrCodeData() : string
    {
        if (!$this->isValid()) {
            throw new InvalidQrBillDataException(
                'The provided data is not valid to generate a qr code. Use getViolations() to find details.'
            );
        }

        $elements = [
            $this->getHeader(),
            $this->getCreditorInformation(),
            $this->getCreditor(),
            $this->getUltimateCreditor() ?: new UltimateCreditor(),
            $this->getPaymentAmountInformation(),
            $this->getUltimateDebtor() ?: new UltimateDebtor(),
            $this->getPaymentReference(),
            $this->getAlternativeSchemes()
        ];

        return $this->extractQrCodeDataFromElements($elements);
    }

    private function extractQrCodeDataFromElements(array $elements) : string
    {
        $qrCodeElements = [];

        foreach ($elements as $element) {
            if ($element instanceof QrCodeData) {
                $qrCodeElements = array_merge($qrCodeElements, $element->getQrCodeData());
            }
        }

        return implode("\r\n", $qrCodeElements);
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
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

        $metadata->addPropertyConstraints('ultimateCreditor', [
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
    }
}
