<?php

use Sprain\SwissQrBill as QrBill;

require __DIR__ . '/../vendor/autoload.php';

// Create a new instance of QrBill, containing default headers with fixed values
$qrBill = QrBill\QrBill::create();

// Add creditor information
// Who will receive the payment and to which bank account?
$creditorInformation = (new QrBill\DataGroups\CreditorInformation())
    ->setIban('CH9300762011623852957');

$creditor = (new QrBill\DataGroups\StructuredAddress())
    ->setName('My Company Ltd.')
    ->setStreet('Bahnhofstrasse')
    ->setBuildingNumber('1')
    ->setPostalCode('8000')
    ->setCity('Zürich')
    ->setCountry('CH');

$qrBill->setCreditorInformation($creditorInformation);
$qrBill->setCreditor($creditor);

// Add debtor information
// Who has to pay the invoice? This part is optional.
$debtor = (new QrBill\DataGroups\StructuredAddress())
    ->setName('Thomas LeClaire')
    ->setStreet('Rue examplaire')
    ->setBuildingNumber('22a')
    ->setPostalCode('1000')
    ->setCity('Lausanne')
    ->setCountry('CH');

$qrBill->setUltimateDebtor($debtor);

// Add payment amount information
// What amount is to be paid? When is it due?
$paymentAmountInformation = (new QrBill\DataGroups\PaymentAmountInformation())
    ->setAmount(25.90)
    ->setCurrency('CHF');

$qrBill->setPaymentAmountInformation($paymentAmountInformation);

// Add payment reference
// This is what you will need to identify incoming payments.
$referenceNumber = (new QrBill\Reference\QrPaymentReferenceGenerator())
    ->setCustomerIdentificationNumber('123456') // you receive this number from your bank
    ->setReferenceNumber('11223344') // a number to match the payment with your other data, e.g. an invoice number
    ->generate();

$paymentReference = (new QrBill\DataGroups\PaymentReference())
    ->setType(QrBill\DataGroups\PaymentReference::TYPE_QR)
    ->setReference($referenceNumber);
$qrBill->setPaymentReference($paymentReference);

// Add additional information
$additionalInformation = (new QrBill\DataGroups\AdditionalInformation())
    ->setMessage('Invoice 11223344, Gardening Work');
$qrBill->setAdditionalInformation($additionalInformation);

// Optionally, make sure all data is valid
if (false === $qrBill->isValid()) {
    die(sprintf('There have been %s violations in your qr bill.', $qrBill->getViolations()->count()));
}

// Get QR code image
$qrBill->getQrCode()->writeFile(__DIR__ . '/qr.png');