<?php

namespace Sprain\Tests\SwissQrBill;

use Sprain\SwissQrBill\DataGroup\Element\AdditionalInformation;
use Sprain\SwissQrBill\DataGroup\Element\CombinedAddress;
use Sprain\SwissQrBill\DataGroup\Element\StructuredAddress;
use Sprain\SwissQrBill\DataGroup\Element\AlternativeScheme;
use Sprain\SwissQrBill\DataGroup\Element\CreditorInformation;
use Sprain\SwissQrBill\DataGroup\Element\Header;
use Sprain\SwissQrBill\DataGroup\Element\PaymentAmountInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentReference;
use Sprain\SwissQrBill\QrBill;

trait TestQrBillCreatorTrait
{

    public function validQrBillsProvider()
    {
        return [
            ['qr-minimal-setup',
                $this->createQrBill([
                    'header',
                    'creditorInformationQrIban',
                    'creditor',
                    'paymentAmountInformation',
                    'paymentReferenceQr'
                ])
            ],
            ['qr-payment-information-without-amount',
                $this->createQrBill([
                    'header',
                    'creditorInformationQrIban',
                    'creditor',
                    'paymentAmountInformationWithoutAmount',
                    'paymentReferenceQr'
                ])
            ],
            ['qr-payment-reference-scor',
                $this->createQrBill([
                    'header',
                    'creditorInformationIban',
                    'creditor',
                    'paymentAmountInformation',
                    'paymentReferenceScor'
                ])
            ],
            ['qr-payment-reference-non',
                $this->createQrBill([
                    'header',
                    'creditorInformationIban',
                    'creditor',
                    'paymentAmountInformation',
                    'paymentReferenceNon'
                ])
            ],
            ['qr-ultimate-debtor',
                $this->createQrBill([
                    'header',
                    'creditorInformationQrIban',
                    'creditor',
                    'paymentAmountInformation',
                    'paymentReferenceQr',
                    'ultimateDebtor'
                ])
            ],
            ['qr-additional-information',
                $this->createQrBill([
                    'header',
                    'creditorInformationQrIban',
                    'creditor',
                    'paymentAmountInformation',
                    'paymentReferenceQr',
                    'additionalInformation'
                ])
            ],
            ['qr-full-set',
                $this->getQrBillFullSet()
            ],
            ['qr-alternative-schemes',
                $this->getQrBillWithAdditonalSchemes()
            ]
        ];
    }

    protected function getQrBillWithAdditonalSchemes()
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

        return $qrBill;
    }

    protected function getQrBillFullSet()
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

        return $qrBill;
    }

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
        $additionalInformation = AdditionalInformation::create('Invoice 1234568');
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