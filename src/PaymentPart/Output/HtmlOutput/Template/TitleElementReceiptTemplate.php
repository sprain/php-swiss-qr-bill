<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template;

class TitleElementReceiptTemplate
{
    public const TEMPLATE = <<<EOT
<h2>{{ {{ title }} }}</h2>
EOT;
}
