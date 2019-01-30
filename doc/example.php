<?php

use Sprain\SwissQrBill as QrBill;

require __DIR__ . '/../vendor/autoload.php';

// Create a new instance of QrBill, containing default headers with fixed values
$qrBill = QrBill\QrBill::create();

// Add creditor information
// Who will receive the payment and to which bank account?
$creditorInformation = (new QrBill\DataGroup\CreditorInformation())
    ->setIban('CH9300762011623852957');

$creditor = (new QrBill\DataGroup\CombinedAddress())
    ->setName('My Company Ltd.')
    ->setAddressLine1('Bahnhofstrasse 1')
    ->setAddressLine2('8000 Zürich')
    ->setCountry('CH');

$qrBill->setCreditorInformation($creditorInformation);
$qrBill->setCreditor($creditor);

// Add debtor information
// Who has to pay the invoice? This part is optional.
//
// Notice how you can use two different styles of addresses: CombinedAddress or StructuredAddress.
// They are interchangeable for creditor as well as debtor.
$debtor = (new QrBill\DataGroup\StructuredAddress())
    ->setName('Thomas LeClaire')
    ->setStreet('Rue examplaire')
    ->setBuildingNumber('22a')
    ->setPostalCode('1000')
    ->setCity('Lausanne')
    ->setCountry('CH');

$qrBill->setUltimateDebtor($debtor);

// Add payment amount information
// What amount is to be paid?
$paymentAmountInformation = (new QrBill\DataGroup\PaymentAmountInformation())
    ->setAmount(25.90)
    ->setCurrency('CHF');

$qrBill->setPaymentAmountInformation($paymentAmountInformation);

// Add payment reference
// This is what you will need to identify incoming payments.
$referenceNumber = (new QrBill\Reference\QrPaymentReferenceGenerator())
    ->setCustomerIdentificationNumber('123456') // you receive this number from your bank
    ->setReferenceNumber('11223344') // a number to match the payment with your other data, e.g. an invoice number
    ->generate();

$paymentReference = (new QrBill\DataGroup\PaymentReference())
    ->setType(QrBill\DataGroup\PaymentReference::TYPE_QR)
    ->setReference($referenceNumber);

$qrBill->setPaymentReference($paymentReference);

// Add additional information
$additionalInformation = (new QrBill\DataGroup\AdditionalInformation())
    ->setMessage('Invoice 11223344, Gardening Work');

$qrBill->setAdditionalInformation($additionalInformation);

// Optionally, make sure all data is valid
if (false === $qrBill->isValid()) {
    die(sprintf('There have been %s violations in your qr bill.', $qrBill->getViolations()->count()));
}

// Get QR code image
$qrBill->getQrCode()->writeFile(__DIR__ . '/qr.png');
