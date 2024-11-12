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

#qr-bill-scissors {
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

#qr-bill-receipt {
    border-right-style: dashed;
}

#qr-bill-separate-info:before {
    content: '\\2702';
    position: relative;
    font-size: 16pt;
    top: 3.6mm;
    left: -23mm;
}

#qr-bill-scissors:after {
    transform: rotate(90deg);
    display: inline-block;
    content: '\\2702';
    position: relative;
    font-weight: normal;
    font-size: 16pt;
    top: 0;
    left: 2.5mm;
}
EOT;

    public const TEMPLATE_VERTICAL_SCISSORS_DOWN = <<<EOT
#qr-bill-scissors:after {
    transform: rotate(-90deg);
    display: inline-block;
    content: '\\2702';
    position: relative;
    font-weight: normal;
    font-size: 16pt;
    top: 78mm;
    left: 3.3mm;
}
EOT;

    public const TEMPLATE_HIDE_TEXT = <<<EOT
#qr-bill-separate-info-text {
    display: none;
}
EOT;

    public const TEMPLATE_TEXT_DOWN_ARROWS = <<<EOT
#qr-bill-separate-info-text:before {
    display: inline-block;
    content: '\\25BC \\25BC \\25BC';
    position: relative;
    font-size: 10pt;
    top: 0;
    left: -1mm;
}

#qr-bill-separate-info-text:after {
    display: inline-block;
    content: '\\25BC \\25BC \\25BC';
    position: relative;
    font-size: 10pt;
    top: 0;
    left: 1mm;
}
EOT;

}
