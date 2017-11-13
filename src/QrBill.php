 <?php

namespace Sprain\SwissQrBill;


class QrBill
{
    private $header;

    private $creditorInformation;

    private $creditor;

    private $ultimateCreditor;

    private $paymentAmountInformation;

    private $ultimateDebtor;

    private $paymentReference;

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

    public function validate()
    {

    }
}