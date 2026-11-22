<?php declare(strict_types=1);

use Com\Tecnick\Pdf\Tcpdf;
use Sprain\SwissQrBill\PaymentPart\Output\DisplayOptions;
use Sprain\SwissQrBill\PaymentPart\Output\TcLibPdfOutput\TcLibPdfOutput;

// 1. tc-lib-pdf does not ship any fonts, so tell it where to find yours.
// See https://github.com/tecnickcom/tc-lib-pdf/blob/main/doc/FONTS.md on how to generate them.
// By default, TcLibPdfOutput needs the core fonts 'helvetica' and 'helveticab',
// plus 'zapfdingbats' if you display scissors or down arrows.
// This example uses the font definitions of the tests of this library.
define('K_PATH_FONTS', __DIR__ . '/../../tests/TestData/TcLibPdfOutput/fonts');

require __DIR__ . '/../../vendor/autoload.php';

// 2. Let's load the base example to define the qr bill contents
require __DIR__ . '/../example.php';

// 3. Create a tc-lib-pdf instance (or use an existing one from your project)
$tcLibPdf = new Tcpdf('mm');
$tcLibPdf->addPage(['format' => 'A4']);

// 4. Create a full payment part for tc-lib-pdf
$output = new TcLibPdfOutput($qrBill, 'en', $tcLibPdf);

// The core fonts only contain the characters of Windows-1252. Any other character, e.g. ł or ą, is printed as "?".
// If your bills may contain such characters, convert a unicode font (the style guide allows Arial, Frutiger,
// Helvetica and Liberation Sans) and pass the keys of its regular and its bold cut, as named by the conversion:
// $output = new TcLibPdfOutput($qrBill, 'en', $tcLibPdf, 0, 0, 'liberationsans', 'liberationsansb');

// 5. Optional, set layout options
$displayOptions = new DisplayOptions();
$displayOptions
    ->setPrintable(false) // true to remove lines for printing on a perforated stationery
    ->setDisplayTextDownArrows(false) // true to show arrows next to separation text, if shown
    ->setDisplayScissors(false) // true to show scissors instead of separation text
    ->setPositionScissorsAtBottom(false) // true to place scissors at the bottom, if shown
;

// 6. Generate the output
$output
    ->setDisplayOptions($displayOptions)
    ->getPaymentPart();

// 7. For demo purposes, let's save the generated example in a file
$examplePath = __DIR__ . "/tclibpdf_example.pdf";
file_put_contents($examplePath, $tcLibPdf->getOutPDFString());

print "PDF example created here : ".$examplePath;
