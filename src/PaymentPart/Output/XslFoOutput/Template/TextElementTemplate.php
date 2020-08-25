<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\XslFoOutput\Template;

class TextElementTemplate
{
    public const TEMPLATE = <<<EOT
<fo:block>{{ text }}</fo:block>

EOT;
}
