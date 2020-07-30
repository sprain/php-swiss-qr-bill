<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\XslFo;

class TextElementTemplate
{
    public const TEMPLATE = <<<EOT
<fo:block>{{ text }}</fo:block>

EOT;
}
