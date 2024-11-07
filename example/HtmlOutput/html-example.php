<?php declare(strict_types=1);

use Sprain\SwissQrBill as QrBill;
use Sprain\SwissQrBill\PaymentPart\Output\PrintOptions;

require __DIR__ . '/../../vendor/autoload.php';

// 1. Let's load the base example to define the qr bill contents
require __DIR__ . '/../example.php';

// 2. Create a full payment part in HTML
$output = new QrBill\PaymentPart\Output\HtmlOutput\HtmlOutput($qrBill, 'en');

// 3. Optional, set layout options
$printOptions = new PrintOptions();
$printOptions
    ->setPrintable(false) // true to remove lines for printing on a perforated stationery
    ->setSeparatorSymbol(false); // true to show scissors instead of text

// 4. Create a full payment part in HTML
$html = $output
    ->setPrintOptions($printOptions)
    ->getPaymentPart();

// 5. For demo purposes, let's save the generated example in a file
$examplePath = __DIR__ . '/html-example.htm';
file_put_contents($examplePath, $html);

print 'HTML example created here: ' . $examplePath;
