<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template;

class PrintableStylesTemplate
{
    public const TEMPLATE = <<<EOT
#qr-bill-separate-info {
	border-bottom: 0.75pt solid black;
}

#qr-bill-separate-info-text {
    display: block;
}

#qr-bill-receipt {
	border-right: 0.2mm solid black;
}
EOT;
}
