<?php

namespace Sprain\SwissQrBill\Tests\DataGroups;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroups\AlternativeScheme;
use Sprain\SwissQrBill\DataGroups\Creditor;
use Sprain\SwissQrBill\DataGroups\CreditorInformation;
use Sprain\SwissQrBill\DataGroups\Header;
use Sprain\SwissQrBill\DataGroups\PaymentAmountInformation;
use Sprain\SwissQrBill\DataGroups\PaymentReference;
use Sprain\SwissQrBill\DataGroups\UltimateCreditor;
use Sprain\SwissQrBill\DataGroups\UltimateDebtor;
use Sprain\SwissQrBill\QrBill;

class QrBillTest extends TestCase
{
    /** @var  QrBill */
    protected $qrBill;

    public function testMinimalSetupIsValid()
    {
        $qrBill = (new QrBill())
            ->setHeader($this->createMock(Header::class))
            ->setCreditorInformation($this->createMock(CreditorInformation::class))
            ->setCreditor($this->createMock(Creditor::class))
            ->setPaymentAmountInformation($this->createMock(PaymentAmountInformation::class))
            ->setPaymentReference($this->createMock(PaymentReference::class));

        $this->assertTrue($qrBill->isValid());
    }

    public function testHeaderIsSetInDefault()
    {
        $qrBill = (QrBill::create())
            ->setCreditorInformation($this->createMock(CreditorInformation::class))
            ->setCreditor($this->createMock(Creditor::class))
            ->setPaymentAmountInformation($this->createMock(PaymentAmountInformation::class))
            ->setPaymentReference($this->createMock(PaymentReference::class));

        $this->assertTrue($qrBill->isValid());
    }

    public function testAllRequiredPropertiesMustBeSet()
    {
        $requiredProperties = 5;

        for ($i = 1; $i <= $requiredProperties; $i++) {
            $qrBill = new QrBill();
            ($i == 1) ?: $qrBill->setHeader($this->createMock(Header::class));
            ($i == 2) ?: $qrBill->setCreditorInformation($this->createMock(CreditorInformation::class));
            ($i == 3) ?: $qrBill->setCreditor($this->createMock(Creditor::class));
            ($i == 4) ?: $qrBill->setPaymentAmountInformation($this->createMock(PaymentAmountInformation::class));
            ($i == 5) ?: $qrBill->setPaymentReference($this->createMock(PaymentReference::class));

            $this->assertSame(1, $qrBill->getViolations()->count(), sprintf('Failed with property number %s.', $i));
        }
    }

    public function testAllOptionPropertiesAreAccepted()
    {
        $qrBill = (new QrBill())
            ->setHeader($this->createMock(Header::class))
            ->setCreditorInformation($this->createMock(CreditorInformation::class))
            ->setCreditor($this->createMock(Creditor::class))
            ->setPaymentAmountInformation($this->createMock(PaymentAmountInformation::class))
            ->setPaymentReference($this->createMock(PaymentReference::class))

            ->setUltimateCreditor($this->createMock(UltimateCreditor::class))
            ->setUltimateDebtor($this->createMock(UltimateDebtor::class))
            ->setAlternativeSchemes([$this->createMock(AlternativeScheme::class)]);

        $this->assertTrue($qrBill->isValid());
    }
}