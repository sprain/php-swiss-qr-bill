<?php declare(strict_types=1);

use Sprain\SwissQrBill as QrBill;
use Sprain\SwissQrBill\PaymentPart\Output\PrintOptions;

require __DIR__ . '/../../vendor/autoload.php';

// 1. Let's load the base example to define the qr bill contents
require __DIR__ . '/../example.php';

// 2. Create a TCPDF instance (or use an existing one from your project)
// – alternatively, an instance of \setasign\Fpdi\Tcpdf\Fpdi() is also accepted by TcPdfOutput.
$tcPdf = new TCPDF('P', 'mm', 'A4', true, 'ISO-8859-1');
$tcPdf->setPrintHeader(false);
$tcPdf->setPrintFooter(false);
$tcPdf->AddPage();

// 3. Optional, set layout options
$options = new PrintOptions();
$options
    ->setPrintable(false)
    ->setSeparatorSymbol(false); // TRUE to show scissors instead of text

// 4. Create a full payment part for TcPDF
$output = new QrBill\PaymentPart\Output\TcPdfOutput\TcPdfOutput($qrBill, 'en', $tcPdf);
$output
    ->setPrintOptions($options)
    ->getPaymentPart();

// 5. For demo purposes, let's save the generated example in a file
$examplePath = __DIR__ . "/tcpdf_example.pdf";
$tcPdf->Output($examplePath, 'F');

print "PDF example created here : ".$examplePath;
