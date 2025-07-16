<?php

declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill;

use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\DataGroup\Element\Header;
use Sprain\SwissQrBill\DataGroup\Element\CombinedAddress;
use Sprain\SwissQrBill\DataGroup\Element\PaymentReference;
use Sprain\SwissQrBill\DataGroup\Element\AlternativeScheme;
use Sprain\SwissQrBill\PaymentPart\Translation\Translation;
use Sprain\SwissQrBill\DataGroup\Element\StructuredAddress;
use Sprain\SwissQrBill\DataGroup\Element\CreditorInformation;
use Sprain\SwissQrBill\DataGroup\Element\AdditionalInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentAmountInformation;

class QrBillTestDataRepository
{
    public function getQrBillWithAdditionalSchemes(): QrBill
    {
        $qrBill = (new QrBillTestDataRepository())->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'paymentReferenceQr',
        ]);

        $qrBill->addAlternativeScheme(AlternativeScheme::create('CC/XRPL/10/bUuK6fwHtfZ3HGAgKvEV7Y5TzHEu8ChUj9'));
        $qrBill->addAlternativeScheme(AlternativeScheme::create('CC/XRPL/10/bUuK6fwHtfZ3HGAgKvEV7Y5TzHEu8ChUj9'));

        return $qrBill;
    }

    public function getQrBillFullSet(): QrBill
    {
        $qrBill = (new QrBillTestDataRepository())->createQrBill([
            'header',
            'creditorInformationQrIban',
            'creditor',
            'paymentAmountInformation',
            'ultimateDebtor',
            'paymentReferenceQr',
            'additionalInformation'
        ]);

        $qrBill->addAlternativeScheme(AlternativeScheme::create('CC/XRPL/10/bUuK6fwHtfZ3HGAgKvEV7Y5TzHEu8ChUj9'));
        $qrBill->addAlternativeScheme(AlternativeScheme::create('CC/XRPL/10/bUuK6fwHtfZ3HGAgKvEV7Y5TzHEu8ChUj9'));

        return $qrBill;
    }

    public function createQrBill(array $elements): QrBill
    {
        $qrBill = QrBill::create();

        foreach ($elements as $element) {
            $this->$element($qrBill);
        }

        return $qrBill;
    }

    public function header(QrBill &$qrBill): void
    {
        $header = Header::create(
            Header::QRTYPE_SPC,
            Header::VERSION_0200,
            Header::CODING_LATIN
        );
        $qrBill->setHeader($header);
    }

    public function invalidHeader(QrBill &$qrBill): void
    {
        // INVALID EMPTY HEADER
        $qrBill->setHeader(Header::create('', '', 5));
    }

    public function creditorInformationIban(QrBill &$qrBill): void
    {
        $creditorInformation = CreditorInformation::create('CH9300762011623852957');
        $qrBill->setCreditorInformation($creditorInformation);
    }

    public function creditorInformationQrIban(QrBill &$qrBill): void
    {
        $creditorInformation = CreditorInformation::create('CH4431999123000889012');
        $qrBill->setCreditorInformation($creditorInformation);
    }

    public function inValidCreditorInformation(QrBill &$qrBill): void
    {
        $creditorInformation = CreditorInformation::create('INVALIDIBAN');
        $qrBill->setCreditorInformation($creditorInformation);
    }

    public function creditor(QrBill &$qrBill): void
    {
        $qrBill->setCreditor($this->structuredAddress());
    }

    public function creditorWithUnsupportedCharacters(QrBill &$qrBill): void
    {
        $qrBill->setCreditor($this->addressWithUnsupportedCharacters());
    }

    public function creditorMediumLong(QrBill &$qrBill): void
    {
        $qrBill->setCreditor($this->mediumLongAddress());
    }

    public function creditorLong(QrBill &$qrBill): void
    {
        $qrBill->setCreditor($this->longAddress());
    }

    public function invalidCreditor(QrBill &$qrBill): void
    {
        $qrBill->setCreditor($this->invalidAddress());
    }

    public function paymentAmountInformation(QrBill &$qrBill): void
    {
        $paymentAmountInformation = PaymentAmountInformation::create(
            'CHF',
            25.90
        );
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);
    }

    public function paymentAmountInformationWithoutAmount(QrBill &$qrBill): void
    {
        $paymentAmountInformation = PaymentAmountInformation::create('EUR');
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);
    }

    public function paymentAmountInformationZeroAmount(QrBill &$qrBill): void
    {
        $paymentAmountInformation = PaymentAmountInformation::create('EUR', 0);
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);
    }

    public function invalidPaymentAmountInformation(QrBill &$qrBill): void
    {
        $paymentAmountInformation = PaymentAmountInformation::create(
            'USD', // invalid currency
            25.90
        );
        $qrBill->setPaymentAmountInformation($paymentAmountInformation);
    }

    public function paymentReferenceQr(QrBill &$qrBill): void
    {
        $paymentReference = PaymentReference::create(
            PaymentReference::TYPE_QR,
            '123456789012345678901234567'
        );
        $qrBill->setPaymentReference($paymentReference);
    }

    public function paymentReferenceScor(QrBill &$qrBill): void
    {
        $paymentReference = PaymentReference::create(
            PaymentReference::TYPE_SCOR,
            'RF18539007547034'
        );
        $qrBill->setPaymentReference($paymentReference);
    }

    public function paymentReferenceNon(QrBill &$qrBill): void
    {
        $paymentReference = PaymentReference::create(
            PaymentReference::TYPE_NON
        );

        $qrBill->setPaymentReference($paymentReference);
    }

    public function invalidPaymentReference(QrBill &$qrBill): void
    {
        $paymentReference = PaymentReference::create(
            PaymentReference::TYPE_QR,
            'INVALID REFERENCE'
        );
        $qrBill->setPaymentReference($paymentReference);
    }

    public function ultimateDebtor(QrBill &$qrBill): void
    {
        $qrBill->setUltimateDebtor($this->combinedAddress());
    }

    public function ultimateDebtorLong(QrBill &$qrBill): void
    {
        $qrBill->setUltimateDebtor($this->longAddress());
    }

    public function internationalUltimateDebtor(QrBill &$qrBill): void
    {
        $qrBill->setUltimateDebtor(CombinedAddress::create(
            'Joachim Kraut',
            'Ewigermeisterstrasse 20',
            '80331 München',
            'DE'
        ));
    }

    public function utf8SpecialCharsUltimateDebtor(QrBill &$qrBill): void
    {
        $qrBill->setUltimateDebtor(CombinedAddress::create(
            'Jôachim Kräutłą',
            'Ewigérmeisterstrasse 20',
            '80331 München',
            'DE'
        ));
    }

    public function invalidUltimateDebtor(QrBill &$qrBill): void
    {
        $qrBill->setUltimateDebtor($this->invalidAddress());
    }

    public function alternativeScheme(QrBill &$qrBill): void
    {
        $alternativeScheme = AlternativeScheme::create('alternativeSchemeParameter');

        $qrBill->addAlternativeScheme($alternativeScheme);
    }

    public function invalidAlternativeScheme(QrBill &$qrBill): void
    {
        $alternativeScheme = (AlternativeScheme::create(''));

        $qrBill->addAlternativeScheme($alternativeScheme);
    }

    public function additionalInformation(QrBill &$qrBill): void
    {
        $additionalInformation = AdditionalInformation::create("Invoice 1234568\nGardening work", 'Bill Information');
        $qrBill->setAdditionalInformation($additionalInformation);
    }

    public function additionalInformationZeroPayment(QrBill &$qrBill): void
    {
        $additionalInformation = AdditionalInformation::create(Translation::get('doNotUseForPayment', 'en'));
        $qrBill->setAdditionalInformation($additionalInformation);
    }

    public function structuredAddress(): StructuredAddress
    {
        return StructuredAddress::createWithStreet(
            'Thomas LeClaire',
            'Rue examplaire',
            '22a',
            '1000',
            'Lausanne',
            'CH'
        );
    }

    public function combinedAddress(): CombinedAddress
    {
        return CombinedAddress::create(
            'Thomas LeClaire',
            'Rue examplaire 22a',
            '1000 Lausanne',
            'CH'
        );
    }

    public function mediumLongAddress(): CombinedAddress
    {
        return CombinedAddress::create(
            'Heaps of Characters International Trading Company of Switzerland GmbH',
            'Rue examplaire 22a',
            '1000 Lausanne',
            'CH'
        );
    }

    public function longAddress(): CombinedAddress
    {
        return CombinedAddress::create(
            'Heaps of Characters International Trading Company of Switzerland GmbH',
            'Street of the Mighty Long Names Where Heroes Live and Villans Die 75',
            '1000 Lausanne au bord du lac, où le soleil brille encore la nuit',
            'CH'
        );
    }

    public function addressWithUnsupportedCharacters(): CombinedAddress
    {
        return CombinedAddress::create(
            'Team «We are the Champions!»',
            'Rue examplaire 22a',
            '1000 Lausanne',
            'CH'
        );
    }

    public function invalidAddress(): CombinedAddress
    {
        return CombinedAddress::create(
            '',
            '',
            '',
            ''
        );
    }
}
