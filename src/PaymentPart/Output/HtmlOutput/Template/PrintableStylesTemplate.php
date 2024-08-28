<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template;

class PrintableStylesTemplate
{
    public const TEMPLATE = <<<EOT
#qr-bill-separate-info {
    border-bottom: 0;
}

#qr-bill-separate-info-text {
    display: none;
}

#qr-bill-receipt {
    border-right: 0;
}
EOT;

    public const TEMPLATE_SCISSORS = <<<EOT

#qr-bill-separate-info {
    border-bottom-style: dashed;
}

#qr-bill-separate-info:before {
    content: '✂';
    position: relative;
    font-size: 16pt;
    top: 3.6mm;
    left: -23mm;
}

#qr-bill-separate-info-text {
    display: none;
}

#qr-bill-receipt {
    border-right-style: dashed;
}

#qr-bill-receipt:after {
    transform: rotate(90deg);
    display: inline-block;
    content: '✂';
    position: relative;
    font-size: 16pt;
    top: -95mm;
    left: 54.5mm;
}
EOT;
}
