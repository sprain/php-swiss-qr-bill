<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template;

class PrintableBordersTemplate
{
    public const TEMPLATE = <<<EOT
#qr-bill-separate-info {
	border-bottom: 0.75pt solid black;
}

#qr-bill-receipt {
	border-right: 0.2mm solid black;
}
EOT;
}
