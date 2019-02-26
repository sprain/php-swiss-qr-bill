<?php

use Sprain\SwissQrBill as QrBill;

require __DIR__ . '/../vendor/autoload.php';

// Create a new instance of QrBill, containing default headers with fixed values
$qrBill = QrBill\QrBill::create();

// Add creditor information
// Who will receive the payment and to which bank account?
$qrBill->setCreditor(
    QrBill\DataGroup\Element\CombinedAddress::create(
        'Robert Schneider AG',
        'Rue du Lac 1268',
        '2501 Biel',
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
        'Pia-Maria Rutschmann-Schnyder',
        'Grosse Marktgasse',
        '28',
        '9400',
        'Rorschach',
        'CH'
    ));

// Add payment amount information
// What amount is to be paid?
$qrBill->setPaymentAmountInformation(
    QrBill\DataGroup\Element\PaymentAmountInformation::create(
        'CHF',
        2500.25
    ));

// Add payment reference
// This is what you will need to identify incoming payments.
$referenceNumber = QrBill\Reference\QrPaymentReferenceGenerator::generate(
    '210000',  // you receive this number from your bank
    '313947143000901' // a number to match the payment with your other data, e.g. an invoice number
);

$qrBill->setPaymentReference(
    QrBill\DataGroup\Element\PaymentReference::create(
        QrBill\DataGroup\Element\PaymentReference::TYPE_QR,
        $referenceNumber
    ));

// Time to output something!
//
// Get the QR code image  …
$qrBill->getQrCode()->writeFile(__DIR__ . '/qr.png');
$qrBill->getQrCode()->writeFile(__DIR__ . '/qr.svg');

// … or output a full payment part
$output = new QrBill\PaymentPart\Output\HtmlOutput\HtmlOutput($qrBill, 'de');
print $output->getPaymentPart();
