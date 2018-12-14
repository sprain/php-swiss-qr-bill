<?php

namespace Sprain\SwissQrBill\Tests\DataGroups;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroups\CombinedAddress;
use Sprain\SwissQrBill\DataGroups\StructuredAddress;
use Sprain\SwissQrBill\DataGroups\AlternativeScheme;
use Sprain\SwissQrBill\DataGroups\CreditorInformation;
use Sprain\SwissQrBill\DataGroups\Header;
use Sprain\SwissQrBill\DataGroups\PaymentAmountInformation;
use Sprain\SwissQrBill\DataGroups\PaymentReference;
use Sprain\SwissQrBill\QrBill;
use Zxing\QrReader;

class QrBillTest extends TestCase
{
    public function testMinimalSetup()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformation',
            'creditor',
            'paymentAmountInformation',
            'paymentReference'
        ]);

        $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-minimal-setup.png');

        $this->assertSame(
            (new QrReader(__DIR__ . '/TestData/qr-minimal-setup.png'))->text(),
            $qrBill->getQrCode()->getText()
        );
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

    public function testPaymentAmountInformationWithoutAmount()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformation',
            'creditor',
            'paymentAmountInformationWithoutAmount',
            'paymentReference'
        ]);

        $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-payment-information-without-amount-and-date.png');

        $this->assertSame(
            (new QrReader(__DIR__ . '/TestData/qr-payment-information-without-amount-and-date.png'))->text(),
            $qrBill->getQrCode()->getText()
        );
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

    public function testPaymentReferenceWithMessage()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformation',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceWithMessage'
        ]);

        $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-payment-reference-with-message.png');

        $this->assertSame(
            (new QrReader(__DIR__ . '/TestData/qr-payment-reference-with-message.png'))->text(),
            $qrBill->getQrCode()->getText()
        );
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

        $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-ultimate-creditor.png');

        $this->assertSame(
            (new QrReader(__DIR__ . '/TestData/qr-ultimate-creditor.png'))->text(),
            $qrBill->getQrCode()->getText()
        );
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

        $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-ultimate-debtor.png');

        $this->assertSame(
            (new QrReader(__DIR__ . '/TestData/qr-ultimate-debtor.png'))->text(),
            $qrBill->getQrCode()->getText()
        );
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

    public function testAlternativeSchemesCanBeAdded()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformation',
            'creditor',
            'paymentAmountInformation',
            'paymentReference',
        ]);

        $qrBill->addAlternativeScheme((new AlternativeScheme())->setParameter('foo'));
        $qrBill->addAlternativeScheme((new AlternativeScheme())->setParameter('foo'));

        $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-alternative-schemes.png');

        $this->assertSame(
            (new QrReader(__DIR__ . '/TestData/qr-alternative-schemes.png'))->text(),
            $qrBill->getQrCode()->getText()
        );
    }

    public function testAlternativeSchemesCanBeSetAtOnce()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformation',
            'creditor',
            'paymentAmountInformation',
            'paymentReference',
        ]);

        $qrBill->setAlternativeSchemes([
            (new AlternativeScheme())->setParameter('foo'),
            (new AlternativeScheme())->setParameter('foo')
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
            'creditorInformation',
            'creditor',
            'paymentAmountInformation',
            'paymentReference',
        ]);

        $qrBill->addAlternativeScheme((new AlternativeScheme())->setParameter('foo'));
        $qrBill->addAlternativeScheme((new AlternativeScheme()));

        $this->assertFalse($qrBill->isValid());
    }

    public function testMaximumTwoAlternativeSchemesAreAllowed()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformation',
            'creditor',
            'paymentAmountInformation',
            'paymentReference'
        ]);

        $qrBill->addAlternativeScheme((new AlternativeScheme())->setParameter('foo'));
        $qrBill->addAlternativeScheme((new AlternativeScheme())->setParameter('foo'));
        $qrBill->addAlternativeScheme((new AlternativeScheme())->setParameter('foo'));

        $this->assertFalse($qrBill->isValid());
    }

    public function testFullQrCodeSet()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformation',
            'creditor',
            'ultimateCreditor',
            'paymentAmountInformation',
            'paymentReferenceWithMessage',
            'ultimateDebtor'
        ]);

        $qrBill->addAlternativeScheme((new AlternativeScheme())->setParameter('foo'));
        $qrBill->addAlternativeScheme((new AlternativeScheme())->setParameter('foo'));

        $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-full-set.png');

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
        $header = (new Header())
            ->setCoding(Header::CODING_LATIN)
            ->setQrType(Header::QRTYPE_SPC)
            ->setVersion(Header::VERSION_0200);
        $qrBill->setHeader($header);
    }

    public function invalidHeader(QrBill &$qrBill)
    {
        // INVALID EMPTY HEADER
        $qrBill->setHeader(new Header());
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
        $qrBill->setCreditor($this->structuredAddress());
    }

    public function invalidCreditor(QrBill &$qrBill)
    {
        $qrBill->setCreditor($this->invalidAddress());
    }

    public function paymentAmountInformation(QrBill &$qrBill)
    {
        $paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25.90)
            ->setCurrency('CHF');
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);
    }

    public function paymentAmountInformationWithoutAmount(QrBill &$qrBill)
    {
        $paymentAmountInformation = (new PaymentAmountInformation())
            ->setCurrency('EUR');
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);
    }

    public function invalidPaymentAmountInformation(QrBill &$qrBill)
    {
        $paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25.90)
            ->setCurrency('USD'); // INVALID CURRENCY
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);
    }

    public function paymentReference(QrBill &$qrBill)
    {
        $paymentReference = (new PaymentReference())
            ->setType(PaymentReference::TYPE_QR)
            ->setReference('123456789012345678901234567');
        $qrBill->setPaymentReference($paymentReference);
    }

    public function paymentReferenceWithMessage(QrBill &$qrBill)
    {
        $paymentReference = (new PaymentReference())
            ->setType(PaymentReference::TYPE_QR)
            ->setReference('123456789012345678901234567')
        ;
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
        $qrBill->setUltimateCreditor($this->structuredAddress());
    }

    public function invalidUltimateCreditor(QrBill &$qrBill)
    {
        $qrBill->setUltimateCreditor($this->invalidAddress());
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
        $alternativeScheme = (new AlternativeScheme())
            ->setParameter('alternativeSchemeParameter');

        $qrBill->addAlternativeScheme($alternativeScheme);
    }

    public function invalidAlternativeScheme(QrBill &$qrBill)
    {
        $alternativeScheme = (new AlternativeScheme());

        $qrBill->addAlternativeScheme($alternativeScheme);
    }

    public function structuredAddress()
    {
        return (new StructuredAddress())
            ->setName('Thomas LeClaire')
            ->setStreet('Rue examplaire')
            ->setBuildingNumber('22a')
            ->setPostalCode('1000')
            ->setCity('Lausanne')
            ->setCountry('CH');
    }

    public function combinedAddress()
    {
        return (new CombinedAddress())
            ->setName('Thomas LeClaire')
            ->setAddressLine1('Rue examplaire 22a')
            ->setAddressLine2('1000 Lausanne')
            ->setCountry('CH');
    }

    public function invalidAddress()
    {
        return (new StructuredAddress())
            // NO NAME!
            ->setStreet('Rue examplaire')
            ->setBuildingNumber('22a')
            ->setPostalCode('1000')
            ->setCity('Lausanne')
            ->setCountry('CH');
    }
}