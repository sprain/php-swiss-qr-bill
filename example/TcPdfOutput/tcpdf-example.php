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

// 3. Create a full payment part for TcPDF
$output = new QrBill\PaymentPart\Output\TcPdfOutput\TcPdfOutput($qrBill, 'en', $tcPdf);

// 4. Optional, set layout options
$printOptions = new PrintOptions();
$printOptions
    ->setPrintable(false) // true to remove lines for printing on a perforated stationery
    ->setSeparatorSymbol(false); // true to show scissors instead of text

// 5. Generate the output
$output
    ->setPrintOptions($printOptions)
    ->getPaymentPart();

// 6. For demo purposes, let's save the generated example in a file
$examplePath = __DIR__ . "/tcpdf_example.pdf";
$tcPdf->Output($examplePath, 'F');

print "PDF example created here : ".$examplePath;
