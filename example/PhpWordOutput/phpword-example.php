<?php declare(strict_types=1);

ini_set('display_errors', 'on');

use PhpOffice\PhpWord\Shared\Converter;
use Sprain\SwissQrBill as QrBill;

require __DIR__ . '/../../vendor/autoload.php';

// 1. Let's load the base example to define the qr bill contents
require __DIR__ . '/../example.php';
// 2. Create a TCPDF instance (or use an existing one from your project)
$phpword = new \PhpOffice\PhpWord\PhpWord();
$section = $phpword->addSection([
		'paperSize' => 'A4',
		'orientation' => 'portrait',
		'marginTop' => Converter::cmToTwip(2.0),
		'marginLeft' => Converter::cmToTwip(2.0),
		'marginBottom' => Converter::cmToTwip(2.0),
		'marginRight' => Converter::cmToTwip(2.0),
		'headerHeight' => Converter::cmToTwip(2.0),
		'footerHeight' => Converter::cmToTwip(2.0),
]);
// 3. Create a full payment part for TcPDF
$output = new QrBill\PaymentPart\Output\PhpWordOutput\PhpWordOutput($qrBill, 'en', $phpword);
$output
    ->setPrintable(false)
    ->getPaymentPart();
// 4. For demo purposes, let's save the generated example in a file
$examplePath = __DIR__ . "/phpword_example.docx";
$phpword->save($examplePath, download: true);

print "Word example created here : ".$examplePath;
