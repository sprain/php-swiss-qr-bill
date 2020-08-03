<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\XslFo;

class TitleElementTemplate
{
    public const TEMPLATE = <<<EOT
<fo:block margin-top="2mm" font-weight="bold" font-size="8pt">{{ {{ title }} }}</fo:block>
EOT;
}
