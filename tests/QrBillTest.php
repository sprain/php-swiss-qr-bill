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
    public function testMinimalSetupIsValid()
    {
        $qrBill = QrBill::create();

        $creditorInformation = (new CreditorInformation())
            ->setIban('CH9300762011623852957');
        $qrBill->setCreditorInformation($creditorInformation);

        $creditor = (new Creditor())
            ->setName('My Company Ltd.')
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setCreditor($creditor);

        $paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25.90)
            ->setCurrency('CHF')
            ->setDueDate(new \DateTime('+30 days'));
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);

        $paymentReference = (new PaymentReference())
            ->setType(PaymentReference::TYPE_QR)
            ->setReference('123456789012345678901234567');
        $qrBill->setPaymentReference($paymentReference);

        $this->assertTrue($qrBill->isValid());
    }

    /**
     * @expectedException Sprain\SwissQrBill\Exception\InvalidQrBillDataException
     */
    public function testCatchInvalidData()
    {
        $qrBill = QrBill::create();
        $qrBill->getQrCode();
    }

    public function testCreditorInformationIsRequired()
    {
        $qrBill = QrBill::create();

        $creditor = (new Creditor())
            ->setName('My Company Ltd.')
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setCreditor($creditor);

        $paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25.90)
            ->setCurrency('CHF')
            ->setDueDate(new \DateTime('+30 days'));
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);

        $paymentReference = (new PaymentReference())
            ->setType(PaymentReference::TYPE_QR)
            ->setReference('123456789012345678901234567');
        $qrBill->setPaymentReference($paymentReference);

        $this->assertSame(1, $qrBill->getViolations()->count());
    }

    public function testCreditorInformationMustBeValid()
    {
        $qrBill = QrBill::create();

        $creditorInformation = (new CreditorInformation())
            ->setIban('INVALIDIBAN');
        $qrBill->setCreditorInformation($creditorInformation);

        $creditor = (new Creditor())
            ->setName('My Company Ltd.')
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setCreditor($creditor);

        $paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25.90)
            ->setCurrency('CHF')
            ->setDueDate(new \DateTime('+30 days'));
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);

        $paymentReference = (new PaymentReference())
            ->setType(PaymentReference::TYPE_QR)
            ->setReference('123456789012345678901234567');
        $qrBill->setPaymentReference($paymentReference);

        $this->assertFalse($qrBill->isValid());
    }

    public function testCreditorIsRequired()
    {
        $qrBill = QrBill::create();

        $creditorInformation = (new CreditorInformation())
            ->setIban('CH9300762011623852957');
        $qrBill->setCreditorInformation($creditorInformation);

        $paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25.90)
            ->setCurrency('CHF')
            ->setDueDate(new \DateTime('+30 days'));
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);

        $paymentReference = (new PaymentReference())
            ->setType(PaymentReference::TYPE_QR)
            ->setReference('123456789012345678901234567');
        $qrBill->setPaymentReference($paymentReference);

        $this->assertSame(1, $qrBill->getViolations()->count());
    }

    public function testCreditorMustBeValid()
    {
        $qrBill = QrBill::create();

        $creditorInformation = (new CreditorInformation())
            ->setIban('CH9300762011623852957');
        $qrBill->setCreditorInformation($creditorInformation);

        $creditor = (new Creditor())
            // NO NAME!
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setCreditor($creditor);

        $paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25.90)
            ->setCurrency('CHF')
            ->setDueDate(new \DateTime('+30 days'));
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);

        $paymentReference = (new PaymentReference())
            ->setType(PaymentReference::TYPE_QR)
            ->setReference('123456789012345678901234567');
        $qrBill->setPaymentReference($paymentReference);

        $this->assertFalse($qrBill->isValid());
    }

    public function testPaymentAmountInformationIsRequired()
    {
        $qrBill = QrBill::create();

        $creditorInformation = (new CreditorInformation())
            ->setIban('CH9300762011623852957');
        $qrBill->setCreditorInformation($creditorInformation);

        $creditor = (new Creditor())
            ->setName('My Company Ltd.')
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setCreditor($creditor);

        $paymentReference = (new PaymentReference())
            ->setType(PaymentReference::TYPE_QR)
            ->setReference('123456789012345678901234567');
        $qrBill->setPaymentReference($paymentReference);

        $this->assertSame(1, $qrBill->getViolations()->count());
    }

    public function testPaymentAmountInformationMustBeValid()
    {
        $qrBill = QrBill::create();

        $creditorInformation = (new CreditorInformation())
            ->setIban('CH9300762011623852957');
        $qrBill->setCreditorInformation($creditorInformation);

        $creditor = (new Creditor())
            ->setName('My Company Ltd.')
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setCreditor($creditor);

        $paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25.90)
            ->setCurrency('USD') // INVALID CURRENCY
            ->setDueDate(new \DateTime('+30 days'));
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);

        $paymentReference = (new PaymentReference())
            ->setType(PaymentReference::TYPE_QR)
            ->setReference('123456789012345678901234567');
        $qrBill->setPaymentReference($paymentReference);

        $this->assertFalse($qrBill->isValid());
    }

    public function testPaymentReferenceIsRequired()
    {
        $qrBill = QrBill::create();

        $creditorInformation = (new CreditorInformation())
            ->setIban('CH9300762011623852957');
        $qrBill->setCreditorInformation($creditorInformation);

        $creditor = (new Creditor())
            ->setName('My Company Ltd.')
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setCreditor($creditor);

        $paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25.90)
            ->setCurrency('CHF')
            ->setDueDate(new \DateTime('+30 days'));
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);

        $this->assertSame(1, $qrBill->getViolations()->count());
    }

    public function testPaymentReferenceMustBeValid()
    {
        $qrBill = QrBill::create();

        $creditorInformation = (new CreditorInformation())
            ->setIban('CH9300762011623852957');
        $qrBill->setCreditorInformation($creditorInformation);

        $creditor = (new Creditor())
            ->setName('My Company Ltd.')
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setCreditor($creditor);

        $paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25.90)
            ->setCurrency('CHF')
            ->setDueDate(new \DateTime('+30 days'));
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);

        $paymentReference = (new PaymentReference())
            ->setType(PaymentReference::TYPE_QR)
            ->setReference('INVALID REFERENCE');
        $qrBill->setPaymentReference($paymentReference);

        $this->assertFalse($qrBill->isValid());
    }

    public function testHeaderIsRequired()
    {
        $qrBill = new QrBill();

        $creditorInformation = (new CreditorInformation())
            ->setIban('CH9300762011623852957');
        $qrBill->setCreditorInformation($creditorInformation);

        $creditor = (new Creditor())
            ->setName('My Company Ltd.')
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setCreditor($creditor);

        $paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25.90)
            ->setCurrency('CHF')
            ->setDueDate(new \DateTime('+30 days'));
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);

        $paymentReference = (new PaymentReference())
            ->setType(PaymentReference::TYPE_QR)
            ->setReference('123456789012345678901234567');
        $qrBill->setPaymentReference($paymentReference);

        $this->assertSame(1, $qrBill->getViolations()->count());
    }

    public function testHeaderMustBeValid()
    {
        $qrBill = new QrBill();

        $header = new Header();
        $qrBill->setHeader($header); // INVALID EMPTY HEADER

        $creditorInformation = (new CreditorInformation())
            ->setIban('CH9300762011623852957');
        $qrBill->setCreditorInformation($creditorInformation);

        $creditor = (new Creditor())
            ->setName('My Company Ltd.')
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setCreditor($creditor);

        $paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25.90)
            ->setCurrency('CHF')
            ->setDueDate(new \DateTime('+30 days'));
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);

        $paymentReference = (new PaymentReference())
            ->setType(PaymentReference::TYPE_QR)
            ->setReference('123456789012345678901234567');
        $qrBill->setPaymentReference($paymentReference);

        $this->assertFalse($qrBill->isValid());
    }

    public function testOptionalUltimateCreditorCanBeSet()
    {
        $qrBill = QrBill::create();

        $creditorInformation = (new CreditorInformation())
            ->setIban('CH9300762011623852957');
        $qrBill->setCreditorInformation($creditorInformation);

        $creditor = (new Creditor())
            ->setName('My Company Ltd.')
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setCreditor($creditor);

        $paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25.90)
            ->setCurrency('CHF')
            ->setDueDate(new \DateTime('+30 days'));
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);

        $paymentReference = (new PaymentReference())
            ->setType(PaymentReference::TYPE_QR)
            ->setReference('123456789012345678901234567');
        $qrBill->setPaymentReference($paymentReference);

        $ultimateCreditor = (new UltimateCreditor())
            ->setName('My Company Holding Ltd.')
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setUltimateCreditor($ultimateCreditor);

        $this->assertTrue($qrBill->isValid());
    }

    public function testOptionalUltimateCreditorMustBeValid()
    {
        $qrBill = QrBill::create();

        $creditorInformation = (new CreditorInformation())
            ->setIban('CH9300762011623852957');
        $qrBill->setCreditorInformation($creditorInformation);

        $creditor = (new Creditor())
            ->setName('My Company Ltd.')
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setCreditor($creditor);

        $paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25.90)
            ->setCurrency('CHF')
            ->setDueDate(new \DateTime('+30 days'));
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);

        $paymentReference = (new PaymentReference())
            ->setType(PaymentReference::TYPE_QR)
            ->setReference('123456789012345678901234567');
        $qrBill->setPaymentReference($paymentReference);

        $ultimateCreditor = (new UltimateCreditor())
            // NO NAME!
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setUltimateCreditor($ultimateCreditor);

        $this->assertFalse($qrBill->isValid());
    }

    public function testOptionalUltimateDebtorCanBeSet()
    {
        $qrBill = QrBill::create();

        $creditorInformation = (new CreditorInformation())
            ->setIban('CH9300762011623852957');
        $qrBill->setCreditorInformation($creditorInformation);

        $creditor = (new Creditor())
            ->setName('My Company Ltd.')
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setCreditor($creditor);

        $paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25.90)
            ->setCurrency('CHF')
            ->setDueDate(new \DateTime('+30 days'));
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);

        $paymentReference = (new PaymentReference())
            ->setType(PaymentReference::TYPE_QR)
            ->setReference('123456789012345678901234567');
        $qrBill->setPaymentReference($paymentReference);

        $ultimateDebtor = (new UltimateDebtor())
            ->setName('Thomas LeClaire')
            ->setStreet('Rue examplaire')
            ->setHouseNumber('22a')
            ->setPostalCode('1000')
            ->setCity('Lausanne')
            ->setCountry('CH');
        $qrBill->setUltimateDebtor($ultimateDebtor);

        $this->assertTrue($qrBill->isValid());
    }

    public function testOptionalUltimateDebtorMustBeValid()
    {
        $qrBill = QrBill::create();

        $creditorInformation = (new CreditorInformation())
            ->setIban('CH9300762011623852957');
        $qrBill->setCreditorInformation($creditorInformation);

        $creditor = (new Creditor())
            ->setName('My Company Ltd.')
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setCreditor($creditor);

        $paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25.90)
            ->setCurrency('CHF')
            ->setDueDate(new \DateTime('+30 days'));
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);

        $paymentReference = (new PaymentReference())
            ->setType(PaymentReference::TYPE_QR)
            ->setReference('123456789012345678901234567');
        $qrBill->setPaymentReference($paymentReference);

        $ultimateDebtor = (new UltimateDebtor())
            // NO NAME!
            ->setStreet('Rue examplaire')
            ->setHouseNumber('22a')
            ->setPostalCode('1000')
            ->setCity('Lausanne')
            ->setCountry('CH');
        $qrBill->setUltimateDebtor($ultimateDebtor);

        $this->assertFalse($qrBill->isValid());
    }
}