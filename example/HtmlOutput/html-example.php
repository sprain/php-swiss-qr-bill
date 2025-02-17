<?php declare(strict_types=1);

use Sprain\SwissQrBill\PaymentPart\Output\DisplayOptions;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\HtmlOutput;

require __DIR__ . '/../../vendor/autoload.php';

// 1. Let's load the base example to define the qr bill contents
require __DIR__ . '/../example.php';

// 2. Create a full payment part in HTML
$output = new HtmlOutput($qrBill, 'en');

// 3. Optional, set layout options
$displayOptions = new DisplayOptions();
$displayOptions
    ->setPrintable(false) // true to remove lines for printing on a perforated stationery
    ->setDisplayTextDownArrows(false) // true to show arrows next to separation text, if shown
    ->setDisplayScissors(false) // true to show scissors instead of separation text
    ->setPositionScissorsAtBottom(false) // true to place scissors at the bottom, if shown
;

// 4. Create a full payment part in HTML
$html = $output
    ->setDisplayOptions($displayOptions)
    ->getPaymentPart();

// 5. For demo purposes, let's save the generated example in a file
$examplePath = __DIR__ . '/html-example.htm';
file_put_contents($examplePath, $html);

print 'HTML example created here: ' . $examplePath;
