<?php

use Sprain\SwissQrBill as QrBill;

require __DIR__ . '/../vendor/autoload.php';

// Create a new instance of QrBill, containing default headers with fixed values
$qrBill = QrBill\QrBill::create();

// Add creditor information
// Who will receive the payment and to which bank account?
$qrBill->setCreditor(
    QrBill\DataGroup\Element\CombinedAddress::create(
        'My Company Ltd.',
        'Bahnhofstrasse 1',
        '8000 Zürich',
        'CH'
    ));

$qrBill->setCreditorInformation(
    QrBill\DataGroup\Element\CreditorInformation::create(
        'CH4431999123000889012'
    ));

// Add debtor information
// Who has to pay the invoice? This part is optional.
//
// Notice how you can use two different styles of addresses: CombinedAddress or StructuredAddress.
// They are interchangeable for creditor as well as debtor.
$qrBill->setUltimateDebtor(
    QrBill\DataGroup\Element\StructuredAddress::createWithStreet(
        'Thomas LeClaire',
        'Rue examplaire',
        '22a',
        '1000',
        'Lausanne',
        'CH'
    ));

// Add payment amount information
// What amount is to be paid?
$qrBill->setPaymentAmountInformation(
    QrBill\DataGroup\Element\PaymentAmountInformation::create(
        'CHF',
        25.90
    ));

// Add payment reference
// This is what you will need to identify incoming payments.
$referenceNumber = QrBill\Reference\QrPaymentReferenceGenerator::generate(
    '123456',  // you receive this number from your bank
    '11223344' // a number to match the payment with your other data, e.g. an invoice number
);

$qrBill->setPaymentReference(
    QrBill\DataGroup\Element\PaymentReference::create(
        QrBill\DataGroup\Element\PaymentReference::TYPE_QR,
        $referenceNumber
    ));

// Add additional information
$qrBill->setAdditionalInformation(
    QrBill\DataGroup\Element\AdditionalInformation::create(
        'Invoice 11223344, Gardening Work'
    ));

// Get QR code image (supports svg and png)
$qrBill->getQrCode()->writeFile(__DIR__ . '/qr.png');
$qrBill->getQrCode()->writeFile(__DIR__ . '/qr.svg');
