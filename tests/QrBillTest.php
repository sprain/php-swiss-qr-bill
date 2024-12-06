<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroup\Element\AlternativeScheme;
use Sprain\SwissQrBill\Exception\InvalidQrBillDataException;
use Sprain\SwissQrBill\QrBill;
use Zxing\QrReader;

final class QrBillTest extends TestCase
{
    use TraitValidQrBillsProvider;

    #[DataProvider('validQrBillsProvider')]
    public function testValidQrBills(string $name, QrBill $qrBill)
    {
        $file = __DIR__ . '/TestData/QrCodes/' . $name . '.png';
        $textFile = __DIR__ . '/TestData/QrCodes/' . $name . '.txt';

        if ($this->regenerateReferenceFiles) {
            $qrBill->getQrCode()->writeFile($file);
            file_put_contents($textFile, $qrBill->getQrCode()->getText());
        }

        $this->assertEquals(
            file_get_contents($textFile),
            $qrBill->getQrCode()->getText()
        );
    }

    public function testAlternativeSchemesCanBeSetAtOnce()
    {
        $qrBill = (new TestQrBillCreator())->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr',
        ]);

        $qrBill->setAlternativeSchemes([
            AlternativeScheme::create('CC/XRPL/10/bUuK6fwHtfZ3HGAgKvEV7Y5TzHEu8ChUj9'),
            AlternativeScheme::create('CC/XRPL/10/bUuK6fwHtfZ3HGAgKvEV7Y5TzHEu8ChUj9')
        ]);

        $this->assertSame(
            (new QrReader(__DIR__ . '/TestData/QrCodes/qr-alternative-schemes.png'))->text(),
            $qrBill->getQrCode()->getText()
        );
    }

    public function testHeaderMustBeValid()
    {
        $qrBill = (new TestQrBillCreator())->createQrBill([
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

        $creator = new TestQrBillCreator();

        $creator->creditorInformationQrIban($qrBill);
        $creator->creditor($qrBill);
        $creator->paymentAmountInformation($qrBill);
        $creator->paymentReferenceQr($qrBill);

        $this->assertTrue($qrBill->isValid());
    }

    public function testCreditorInformationIsRequired()
    {
        $qrBill = (new TestQrBillCreator())->createQrBill([
            'header',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr'
        ]);

        $this->assertSame(1, $qrBill->getViolations()->count());
    }

    public function testCreditorInformationMustBeValid()
    {
        $qrBill = (new TestQrBillCreator())->createQrBill([
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
        $qrBill = (new TestQrBillCreator())->createQrBill([
            'header',
            'creditorInformationQrIban',
            'paymentAmountInformation',
            'paymentReferenceQr'
        ]);

        $this->assertSame(1, $qrBill->getViolations()->count());
    }

    public function testCreditorMustBeValid()
    {
        $qrBill = (new TestQrBillCreator())->createQrBill([
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
        $qrBill = (new TestQrBillCreator())->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentReferenceQr'
        ]);

        $this->assertSame(1, $qrBill->getViolations()->count());
    }

    public function testPaymentAmountInformationMustBeValid()
    {
        $qrBill = (new TestQrBillCreator())->createQrBill([
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
        $qrBill = (new TestQrBillCreator())->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
        ]);

        $this->assertSame(1, $qrBill->getViolations()->count());
    }

    public function testPaymentReferenceMustBeValid()
    {
        $qrBill = (new TestQrBillCreator())->createQrBill([
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
        $qrBill = (new TestQrBillCreator())->createQrBill([
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
        $qrBill = (new TestQrBillCreator())->createQrBill([
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
        $qrBill = (new TestQrBillCreator())->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr',
        ]);

        $qrBill->addAlternativeScheme(AlternativeScheme::create('CC/XRPL/10/bUuK6fwHtfZ3HGAgKvEV7Y5TzHEu8ChUj9'));
        $qrBill->addAlternativeScheme(AlternativeScheme::create(''));

        $this->assertFalse($qrBill->isValid());
    }

    public function testMaximumTwoAlternativeSchemesAreAllowed()
    {
        $qrBill = (new TestQrBillCreator())->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr'
        ]);

        $qrBill->addAlternativeScheme(AlternativeScheme::create('CC/XRPL/10/bUuK6fwHtfZ3HGAgKvEV7Y5TzHEu8ChUj9'));
        $qrBill->addAlternativeScheme(AlternativeScheme::create('CC/XRPL/10/bUuK6fwHtfZ3HGAgKvEV7Y5TzHEu8ChUj9'));
        $qrBill->addAlternativeScheme(AlternativeScheme::create('CC/XRPL/10/bUuK6fwHtfZ3HGAgKvEV7Y5TzHEu8ChUj9'));

        $this->assertFalse($qrBill->isValid());
    }

    public function testItReplacesUnsupportedCharacters()
    {
        $qrBill = (new TestQrBillCreator())->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditorWithUnsupportedCharacters',
            'paymentAmountInformation',
            'paymentReferenceQr',
        ]);

        $this->assertStringContainsString(
            'Team We are the Champions!',
            $qrBill->getQrCode()->getText()
        );
    }

    public function testItConsidersReplacementCharacters()
    {
        $qrBill = (new TestQrBillCreator())->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditorWithUnsupportedCharacters',
            'paymentAmountInformation',
            'paymentReferenceQr',
        ]);

        $unsupportedCharacterReplacements = [
            '«' => '"',
            '»' => '"',
        ];

        $qrBill->setUnsupportedCharacterReplacements($unsupportedCharacterReplacements);

        $this->assertStringContainsString(
            'Team "We are the Champions!"',
            $qrBill->getQrCode()->getText()
        );
    }

    public function testCatchInvalidData()
    {
        $this->expectException(InvalidQrBillDataException::class);

        $qrBill = QrBill::create();
        $qrBill->getQrCode();
    }
}
