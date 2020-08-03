<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\XslFo;

class TitleElementReceiptTemplate
{
    public const TEMPLATE = <<<EOT
<fo:block margin-top="1.5mm" font-weight="bold" font-size="6pt">{{ {{ title }} }}</fo:block>
EOT;
}
