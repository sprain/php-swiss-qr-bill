<?php

namespace Sprain\SwissQrBill;

use Sprain\SwissQrBill\DataGroups\AlternativeScheme;
use Sprain\SwissQrBill\DataGroups\Creditor;
use Sprain\SwissQrBill\DataGroups\CreditorInformation;
use Sprain\SwissQrBill\DataGroups\Header;
use Sprain\SwissQrBill\DataGroups\PaymentAmountInformation;
use Sprain\SwissQrBill\DataGroups\PaymentReference;
use Sprain\SwissQrBill\DataGroups\UltimateCreditor;
use Sprain\SwissQrBill\DataGroups\UltimateDebtor;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QrBill
{
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

    /** @var ValidatorInterface */
    private $validator;


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

    public function getUltimateCreditor(): UltimateCreditor
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

    public function getUltimateDebtor(): UltimateDebtor
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

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraints('header', [
            new Assert\NotNull()
        ]);

        $metadata->addPropertyConstraints('creditorInformation', [
            new Assert\NotNull()
        ]);

        $metadata->addPropertyConstraints('creditor', [
            new Assert\NotNull()
        ]);

        $metadata->addPropertyConstraints('paymentAmountInformation', [
            new Assert\NotNull()
        ]);

        $metadata->addPropertyConstraints('paymentReference', [
            new Assert\NotNull()
        ]);
    }

    public function validate()
    {
        if (null == $this->validator) {
            $this->validator = Validation::createValidatorBuilder()
                ->addMethodMapping('loadValidatorMetadata')
                ->getValidator();
        }

        return $this->validator->validate($this);
    }
}