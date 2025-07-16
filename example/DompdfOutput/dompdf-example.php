<?php declare(strict_types=1);

use Sprain\SwissQrBill\PaymentPart\Output\DisplayOptions;
use Sprain\SwissQrBill\PaymentPart\Output\DompdfOutput\DompdfOutput;
use Dompdf\Dompdf;

require __DIR__ . '/../../vendor/autoload.php';

// 1. Let's load the base example to define the qr bill contents
require __DIR__ . '/../example.php';

// 2. Create a full payment part in HTML
$output = new DompdfOutput($qrBill, 'en');

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

$dompdf = new Dompdf();
$dompdf->setPaper('A4', 'portrait');

// important: needs UTF-8
$html = <<<EOT
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>$html</body>
</html>
EOT;

$dompdf->loadHtml($html, 'UTF-8');
$dompdf->render();

// 5. For demo purposes, let's save the generated example in a file
$examplePath = __DIR__ . '/dompdf-example.pdf';
file_put_contents($examplePath, $dompdf->output());

print 'Dompdf example created here: ' . $examplePath;
