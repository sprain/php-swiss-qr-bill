<?php

namespace Sprain\Tests\SwissQrBill;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroup\Element\AlternativeScheme;
use Sprain\SwissQrBill\QrBill;
use Zxing\QrReader;

class QrBillTest extends TestCase
{
    use TestQrBillCreatorTrait;

    private $regenerateReferenceQrCodes = false;

    /**
     * @dataProvider validQrBillsProvider
     */
    public function testValidQrBills(string $name, QrBill $qrBill)
    {
        $file = __DIR__ . '/TestData/QrCodes/' . $name . '.png';

        if ($this->regenerateReferenceQrCodes) {
            $qrBill->getQrCode()->writeFile($file);
        }

        $this->assertSame(
            (new QrReader($file))->text(),
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
            (new QrReader(__DIR__ . '/TestData/QrCodes/qr-alternative-schemes.png'))->text(),
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

    /**
     * @expectedException \Sprain\SwissQrBill\Exception\InvalidQrBillDataException
     */
    public function testCatchInvalidData()
    {
        $qrBill = QrBill::create();
        $qrBill->getQrCode();
    }
}