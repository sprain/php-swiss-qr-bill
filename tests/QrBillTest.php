<?php

namespace Sprain\SwissQrBill\Tests\Element;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroup\Element\AdditionalInformation;
use Sprain\SwissQrBill\DataGroup\Element\CombinedAddress;
use Sprain\SwissQrBill\DataGroup\Element\StructuredAddress;
use Sprain\SwissQrBill\DataGroup\Element\AlternativeScheme;
use Sprain\SwissQrBill\DataGroup\Element\CreditorInformation;
use Sprain\SwissQrBill\DataGroup\Element\Header;
use Sprain\SwissQrBill\DataGroup\Element\PaymentAmountInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentReference;
use Sprain\SwissQrBill\QrBill;
use Zxing\QrReader;

class QrBillTest extends TestCase
{
    public function testMinimalSetup()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr'
        ]);

        # $qrBill
        #    ->setErrorCorrectionLevel(QrBill::ERROR_CORRECTION_LEVEL_MEDIUM) // due to limitations of QrReader class used in assert below
        #    ->getQrCode()
        #    ->writeFile(__DIR__ . '/TestData/qr-minimal-setup.png');

        foreach ($qrBill->getViolations() as $violation) {
            print $violation->getMessage()."\n";
        }

        $this->assertSame(
            (new QrReader(__DIR__ . '/TestData/qr-minimal-setup.png'))->text(),
            $qrBill->getQrCode()->getText()
        );
    }

    public function testHeaderIsRequired()
    {
        $qrBill = $this->createQrBill([
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr'
        ]);

        $this->assertSame(1, $qrBill->getViolations()->count());
    }

    public function testHeaderMustBeValid()
    {
        $qrBill = $this->createQrBill([
            'invalidHeader',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr'
        ]);

        $this->assertFalse($qrBill->isValid());
    }

    public function testHeaderIsCreatedInStaticConstructor()
    {
        $qrBill = QrBill::create();

        $this->creditorInformationQrIban($qrBill);
        $this->creditor($qrBill);
        $this->paymentAmountInformation($qrBill);
        $this->paymentReferenceQr($qrBill);

        $this->assertTrue($qrBill->isValid());
    }

    public function testCreditorInformationIsRequired()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr'
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
            'paymentReferenceQr'
        ]);

        $this->assertFalse($qrBill->isValid());
    }

    public function testCreditorIsRequired()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationQrIban',
            'paymentAmountInformation',
            'paymentReferenceQr'
        ]);

        $this->assertSame(1, $qrBill->getViolations()->count());
    }

    public function testCreditorMustBeValid()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationQrIban',
            'invalidCreditor',
            'paymentAmountInformation',
            'paymentReferenceQr'
        ]);

        $this->assertFalse($qrBill->isValid());
    }

    public function testPaymentAmountInformationIsRequired()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentReferenceQr'
        ]);

        $this->assertSame(1, $qrBill->getViolations()->count());
    }

    public function testPaymentAmountInformationMustBeValid()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'invalidPaymentAmountInformation',
            'paymentReferenceQr'
        ]);

        $this->assertFalse($qrBill->isValid());
    }

    public function testPaymentAmountInformationWithoutAmount()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformationWithoutAmount',
            'paymentReferenceQr'
        ]);

        # $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-payment-information-without-amount.png');

        $this->assertSame(
            (new QrReader(__DIR__ . '/TestData/qr-payment-information-without-amount.png'))->text(),
            $qrBill->getQrCode()->getText()
        );
    }

    public function testPaymentReferenceIsRequired()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
        ]);

        $this->assertSame(1, $qrBill->getViolations()->count());
    }

    public function testPaymentReferenceMustBeValid()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'invalidPaymentReference'
        ]);

        $this->assertFalse($qrBill->isValid());
    }

    public function testPaymentReferenceScor()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceScor'
        ]);

        # $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-payment-reference-scor.png');

        $this->assertSame(
            (new QrReader(__DIR__ . '/TestData/qr-payment-reference-scor.png'))->text(),
            $qrBill->getQrCode()->getText()
        );
    }

    public function testPaymentReferenceNon()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceNon'
        ]);

        # $qrBill
        #     ->setErrorCorrectionLevel(QrBill::ERROR_CORRECTION_LEVEL_MEDIUM) // due to limitations of QrReader class used in assert below
        #     ->getQrCode()->writeFile(__DIR__ . '/TestData/qr-payment-reference-non.png');

        $this->assertSame(
            (new QrReader(__DIR__ . '/TestData/qr-payment-reference-non.png'))->text(),
            $qrBill->getQrCode()->getText()
        );
    }

    public function testNonMatchingAccountAndReference()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr'
        ]);

        $this->assertFalse($qrBill->isValid());
    }

    public function testOptionalUltimateDebtorCanBeSet()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr',
            'ultimateDebtor'
        ]);

        # $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-ultimate-debtor.png');

        $this->assertSame(
            (new QrReader(__DIR__ . '/TestData/qr-ultimate-debtor.png'))->text(),
            $qrBill->getQrCode()->getText()
        );
    }

    public function testOptionalUltimateDebtorMustBeValid()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr',
            'invalidUltimateDebtor'
        ]);

        $this->assertFalse($qrBill->isValid());
    }

    public function testAlternativeSchemesCanBeAdded()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr',
        ]);

        $qrBill->addAlternativeScheme(AlternativeScheme::create('foo'));
        $qrBill->addAlternativeScheme(AlternativeScheme::create('foo'));

        # $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-alternative-schemes.png');

        $this->assertSame(
            (new QrReader(__DIR__ . '/TestData/qr-alternative-schemes.png'))->text(),
            $qrBill->getQrCode()->getText()
        );
    }

    public function testAlternativeSchemesCanBeSetAtOnce()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr',
        ]);

        $qrBill->setAlternativeSchemes([
            AlternativeScheme::create('foo'),
            AlternativeScheme::create('foo')
        ]);

        $this->assertSame(
            (new QrReader(__DIR__ . '/TestData/qr-alternative-schemes.png'))->text(),
            $qrBill->getQrCode()->getText()
        );
    }

    public function testAlternativeSchemesMustBeValid()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr',
        ]);

        $qrBill->addAlternativeScheme(AlternativeScheme::create('foo'));
        $qrBill->addAlternativeScheme((new AlternativeScheme()));

        $this->assertFalse($qrBill->isValid());
    }

    public function testMaximumTwoAlternativeSchemesAreAllowed()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr'
        ]);

        $qrBill->addAlternativeScheme(AlternativeScheme::create('foo'));
        $qrBill->addAlternativeScheme(AlternativeScheme::create('foo'));
        $qrBill->addAlternativeScheme(AlternativeScheme::create('foo'));

        $this->assertFalse($qrBill->isValid());
    }

    public function testAdditionalInformationCanBeAdded()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr',
            'additionalInformation'
        ]);

        # $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-additional-information.png');

        $this->assertSame(
            (new QrReader(__DIR__ . '/TestData/qr-additional-information.png'))->text(),
            $qrBill->getQrCode()->getText()
        );
    }

    public function testFullQrCodeSet()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'ultimateDebtor',
            'paymentReferenceQr',
            'additionalInformation'
        ]);

        $qrBill->addAlternativeScheme(AlternativeScheme::create('foo'));
        $qrBill->addAlternativeScheme(AlternativeScheme::create('foo'));

        # $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-full-set.png');

        $this->assertSame(
            (new QrReader(__DIR__ . '/TestData/qr-full-set.png'))->text(),
            $qrBill->getQrCode()->getText()
        );
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
        $header = Header::create(
            Header::QRTYPE_SPC,
            Header::VERSION_0200,
            Header::CODING_LATIN
        );
        $qrBill->setHeader($header);
    }

    public function invalidHeader(QrBill &$qrBill)
    {
        // INVALID EMPTY HEADER
        $qrBill->setHeader(new Header());
    }

    public function creditorInformationIban(QrBill &$qrBill)
    {
        $creditorInformation = CreditorInformation::create('CH9300762011623852957');
        $qrBill->setCreditorInformation($creditorInformation);
    }

    public function creditorInformationQrIban(QrBill &$qrBill)
    {
        $creditorInformation = CreditorInformation::create('CH4431999123000889012');
        $qrBill->setCreditorInformation($creditorInformation);
    }

    public function inValidCreditorInformation(QrBill &$qrBill)
    {
        $creditorInformation = CreditorInformation::create('INVALIDIBAN');
        $qrBill->setCreditorInformation($creditorInformation);
    }

    public function creditor(QrBill &$qrBill)
    {
        $qrBill->setCreditor($this->structuredAddress());
    }

    public function invalidCreditor(QrBill &$qrBill)
    {
        $qrBill->setCreditor($this->invalidAddress());
    }

    public function paymentAmountInformation(QrBill &$qrBill)
    {
        $paymentAmountInformation = PaymentAmountInformation::create(
            'CHF',
            25.90
        );
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);
    }

    public function paymentAmountInformationWithoutAmount(QrBill &$qrBill)
    {
        $paymentAmountInformation = PaymentAmountInformation::create('EUR');
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);
    }

    public function invalidPaymentAmountInformation(QrBill &$qrBill)
    {
        $paymentAmountInformation = PaymentAmountInformation::create(
            'USD', // invalid currency
            25.90
        );
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);
    }

    public function paymentReferenceQr(QrBill &$qrBill)
    {
        $paymentReference = PaymentReference::create(
            PaymentReference::TYPE_QR,
            '123456789012345678901234567'
        );
        $qrBill->setPaymentReference($paymentReference);
    }

    public function paymentReferenceScor(QrBill &$qrBill)
    {
        $paymentReference = PaymentReference::create(
            PaymentReference::TYPE_SCOR,
            'RF18539007547034'
        );
        $qrBill->setPaymentReference($paymentReference);
    }

    public function paymentReferenceNon(QrBill &$qrBill)
    {
        $paymentReference = PaymentReference::create(
            PaymentReference::TYPE_NON
        );

        $qrBill->setPaymentReference($paymentReference);
    }

    public function invalidPaymentReference(QrBill &$qrBill)
    {
        $paymentReference = PaymentReference::create(
            PaymentReference::TYPE_QR,
            'INVALID REFERENCE'
        );
        $qrBill->setPaymentReference($paymentReference);
    }

    public function ultimateDebtor(QrBill &$qrBill)
    {
        $qrBill->setUltimateDebtor($this->combinedAddress());
    }

    public function invalidUltimateDebtor(QrBill &$qrBill)
    {
        $qrBill->setUltimateDebtor($this->invalidAddress());
    }

    public function alternativeScheme(QrBill &$qrBill)
    {
        $alternativeScheme = AlternativeScheme::create('alternativeSchemeParameter');

        $qrBill->addAlternativeScheme($alternativeScheme);
    }

    public function invalidAlternativeScheme(QrBill &$qrBill)
    {
        $alternativeScheme = (new AlternativeScheme());

        $qrBill->addAlternativeScheme($alternativeScheme);
    }

    public function additionalInformation(QrBill &$qrBill)
    {
        $additionalInformation = AdditionalInformation::create('Invoice 123456');
        $qrBill->setAdditionalInformation($additionalInformation);
    }

    public function structuredAddress()
    {
        return StructuredAddress::createWithStreet(
            'Thomas LeClaire',
            'Rue examplaire',
            '22a',
            '1000',
            'Lausanne',
            'CH',''
        );
    }

    public function combinedAddress()
    {
        return CombinedAddress::create(
            'Thomas LeClaire',
            'Rue examplaire 22a',
            '1000 Lausanne',
            'CH'
        );
    }

    public function invalidAddress()
    {
        return new CombinedAddress();
    }
}