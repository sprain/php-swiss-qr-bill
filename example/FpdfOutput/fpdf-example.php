<?php

use Sprain\SwissQrBill\PaymentPart\Output\FpdfOutput\Template\QrBillFooter;

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

// 1. Let's load the base example to define the qr bill contents
require dirname(__DIR__) . '/example.php';

// 2. Create a FPDF instance (or use an existing one from your project)
$fpdf = new QrBillFooter('P', 'mm', 'A4');
$fpdf->AddPage();

// 3. Create a full payment part for FPDF
$output = new Sprain\SwissQrBill\PaymentPart\Output\FpdfOutput\FpdfOutput($qrBill, 'en', $fpdf);
$output
    ->setPrintable(false)
    ->getPaymentPart();

// 4. For demo purposes, let's save the generated example in a file
$examplePath = __DIR__ . "/fpdf_example.pdf";
$fpdf->Output($examplePath, 'F');

if (file_exists(dirname(__DIR__) . '/qr.svg')) {
    unlink(dirname(__DIR__) . '/qr.svg');
    unlink(dirname(__DIR__) . '/qr.png');
}

print "PDF example created here : " . $examplePath;
