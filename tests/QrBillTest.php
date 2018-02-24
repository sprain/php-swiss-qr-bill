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
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformation',
            'creditor',
            'paymentAmountInformation',
            'paymentReference'
        ]);

        $this->assertTrue($qrBill->isValid());
    }

    public function testHeaderIsRequired()
    {
        $qrBill = $this->createQrBill([
            'creditorInformation',
            'creditor',
            'paymentAmountInformation',
            'paymentReference'
        ]);

        $this->assertSame(1, $qrBill->getViolations()->count());
    }

    public function testHeaderMustBeValid()
    {
        $qrBill = $this->createQrBill([
            'invalidHeader',
            'creditorInformation',
            'creditor',
            'paymentAmountInformation',
            'paymentReference'
        ]);

        $this->assertFalse($qrBill->isValid());
    }

    public function testHeaderIsCreatedInStaticConstructor()
    {
        $qrBill = QrBill::create();

        $this->creditorInformation($qrBill);
        $this->creditor($qrBill);
        $this->paymentAmountInformation($qrBill);
        $this->paymentReference($qrBill);

        $this->assertTrue($qrBill->isValid());
    }

    public function testCreditorInformationIsRequired()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditor',
            'paymentAmountInformation',
            'paymentReference'
        ]);

        $this->assertSame(1, $qrBill->getViolations()->count());
    }

    public function testCreditorInformationMustBeValid()
    {
        $qrBill = $this->createQrBill([
            'header',
            'invalidCreditorInformation',
            'creditor',
            'paymentAmountInformation',
            'paymentReference'
        ]);

        $this->assertFalse($qrBill->isValid());
    }

    public function testCreditorIsRequired()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformation',
            'paymentAmountInformation',
            'paymentReference'
        ]);

        $this->assertSame(1, $qrBill->getViolations()->count());
    }

    public function testCreditorMustBeValid()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformation',
            'invalidCreditor',
            'paymentAmountInformation',
            'paymentReference'
        ]);

        $this->assertFalse($qrBill->isValid());
    }

    public function testPaymentAmountInformationIsRequired()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformation',
            'creditor',
            'paymentReference'
        ]);

        $this->assertSame(1, $qrBill->getViolations()->count());
    }

    public function testPaymentAmountInformationMustBeValid()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformation',
            'creditor',
            'invalidPaymentAmountInformation',
            'paymentReference'
        ]);

        $this->assertFalse($qrBill->isValid());
    }

    public function testPaymentReferenceIsRequired()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformation',
            'creditor',
            'paymentAmountInformation',
        ]);

        $this->assertSame(1, $qrBill->getViolations()->count());
    }

    public function testPaymentReferenceMustBeValid()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformation',
            'creditor',
            'paymentAmountInformation',
            'invalidPaymentReference'
        ]);

        $this->assertFalse($qrBill->isValid());
    }

    public function testOptionalUltimateCreditorCanBeSet()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformation',
            'creditor',
            'paymentAmountInformation',
            'paymentReference',
            'ultimateCreditor'
        ]);

        $this->assertTrue($qrBill->isValid());
    }

    public function testOptionalUltimateCreditorMustBeValid()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformation',
            'creditor',
            'paymentAmountInformation',
            'paymentReference',
            'invalidUltimateCreditor'
        ]);

        $this->assertFalse($qrBill->isValid());
    }

    public function testOptionalUltimateDebtorCanBeSet()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformation',
            'creditor',
            'paymentAmountInformation',
            'paymentReference',
            'ultimateDebtor'
        ]);

        $this->assertTrue($qrBill->isValid());
    }

    public function testOptionalUltimateDebtorMustBeValid()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformation',
            'creditor',
            'paymentAmountInformation',
            'paymentReference',
            'invalidUltimateDebtor'
        ]);

        $this->assertFalse($qrBill->isValid());
    }
    
    /**
     * @expectedException \Sprain\SwissQrBill\Exception\InvalidQrBillDataException
     */
    public function testCatchInvalidData()
    {
        $qrBill = QrBill::create();
        $qrBill->getQrCode();
    }

    /***
     * HELPER METHODS TO CREATE QR BILL CONTENTS
     */

    public function createQrBill(array $elements)
    {
        $qrBill = new QrBill();

        foreach($elements as $element) {
            $this->$element($qrBill);
        }

        return $qrBill;
    }

    public function header(QrBill &$qrBill)
    {
        $header = new Header();
        $header->setCoding(Header::CODING_LATIN);
        $header->setQrType(Header::QRTYPE_SPC);
        $header->setVersion(Header::VERSION_0100);

        $qrBill->setHeader($header);
    }

    public function invalidHeader(QrBill &$qrBill)
    {
        // INVALID EMPTY HEADER
        $header = new Header();

        $qrBill->setHeader($header);
    }

    public function creditorInformation(QrBill &$qrBill)
    {
        $creditorInformation = (new CreditorInformation())
            ->setIban('CH9300762011623852957');
        $qrBill->setCreditorInformation($creditorInformation);
    }

    public function inValidCreditorInformation(QrBill &$qrBill)
    {
        $creditorInformation = (new CreditorInformation())
            ->setIban('INVALIDIBAN');
        $qrBill->setCreditorInformation($creditorInformation);
    }

    public function creditor(QrBill &$qrBill)
    {
        $creditor = (new Creditor())
            ->setName('My Company Ltd.')
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setCreditor($creditor);
    }

    public function invalidCreditor(QrBill &$qrBill)
    {
        $creditor = (new Creditor())
            // NO NAME!
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setCreditor($creditor);
    }

    public function paymentAmountInformation(QrBill &$qrBill)
    {
        $paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25.90)
            ->setCurrency('CHF')
            ->setDueDate(new \DateTime('+30 days'));
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);
    }

    public function invalidPaymentAmountInformation(QrBill &$qrBill)
    {
        $paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25.90)
            ->setCurrency('USD') // INVALID CURRENCY
            ->setDueDate(new \DateTime('+30 days'));
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);
    }

    public function paymentReference(QrBill &$qrBill)
    {
        $paymentReference = (new PaymentReference())
            ->setType(PaymentReference::TYPE_QR)
            ->setReference('123456789012345678901234567');
        $qrBill->setPaymentReference($paymentReference);
    }

    public function invalidPaymentReference(QrBill &$qrBill)
    {
        $paymentReference = (new PaymentReference())
            ->setType(PaymentReference::TYPE_QR)
            ->setReference('INVALID REFERENCE');
        $qrBill->setPaymentReference($paymentReference);
    }

    public function ultimateCreditor(QrBill &$qrBill)
    {
        $ultimateCreditor = (new UltimateCreditor())
            ->setName('My Company Holding Ltd.')
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setUltimateCreditor($ultimateCreditor);
    }

    public function invalidUltimateCreditor(QrBill &$qrBill)
    {
        $ultimateCreditor = (new UltimateCreditor())
            // NO NAME!
            ->setStreet('Bahnhofstrasse')
            ->setHouseNumber('1')
            ->setPostalCode('8000')
            ->setCity('Zürich')
            ->setCountry('CH');
        $qrBill->setUltimateCreditor($ultimateCreditor);
    }

    public function ultimateDebtor(QrBill &$qrBill)
    {
        $ultimateDebtor = (new UltimateDebtor())
            ->setName('Thomas LeClaire')
            ->setStreet('Rue examplaire')
            ->setHouseNumber('22a')
            ->setPostalCode('1000')
            ->setCity('Lausanne')
            ->setCountry('CH');
        $qrBill->setUltimateDebtor($ultimateDebtor);
    }

    public function invalidUltimateDebtor(QrBill &$qrBill)
    {
        $ultimateDebtor = (new UltimateDebtor())
            // NO NAME!
            ->setStreet('Rue examplaire')
            ->setHouseNumber('22a')
            ->setPostalCode('1000')
            ->setCity('Lausanne')
            ->setCountry('CH');
        $qrBill->setUltimateDebtor($ultimateDebtor);
    }
}