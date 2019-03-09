<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template;

class PrintableStylesTemplate
{
    public const TEMPLATE = <<<EOT
#qr-bill {
    border: 0;
}
#qr-bill-receipt {
    border-right: 0;
}
EOT;
}
