<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\XslFo;

class TitleElementReceiptTemplate
{
    public const TEMPLATE = <<<EOT
<fo:block font-weight="bold" font-size="6pt">{{ {{ title }} }}</fo:block>
EOT;
}
