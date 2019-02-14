<?php

namespace Sprain\Tests\SwissQrBill\DataGroup\Element;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroup\Element\AlternativeScheme;
use Sprain\SwissQrBill\QrBill;
use Sprain\Tests\SwissQrBill\TestQrBillCreatorTrait;
use Zxing\QrReader;

class QrBillTest extends TestCase
{
    use TestQrBillCreatorTrait;

    public function testMinimalSetup()
    {
        $qrBill = $this->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr'
        ]);

        if ($this->regenerateReferenceQrCodes) {
            $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-minimal-setup.png');
        }

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

        if ($this->regenerateReferenceQrCodes) {
            $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-payment-information-without-amount.png');
        }

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

        if ($this->regenerateReferenceQrCodes) {
            $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-payment-reference-scor.png');
        }

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

        if ($this->regenerateReferenceQrCodes) {
            $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-payment-reference-non.png');
        }

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

        if ($this->regenerateReferenceQrCodes) {
            $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-ultimate-debtor.png');
        }

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

        if ($this->regenerateReferenceQrCodes) {
            $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-alternative-schemes.png');
        }

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

        if ($this->regenerateReferenceQrCodes) {
            $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-additional-information.png');
        }

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

        if ($this->regenerateReferenceQrCodes) {
            $qrBill->getQrCode()->writeFile(__DIR__ . '/TestData/qr-full-set.png');
        }

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
}