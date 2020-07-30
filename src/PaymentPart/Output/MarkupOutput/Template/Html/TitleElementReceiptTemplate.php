<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\Html;

class TitleElementReceiptTemplate
{
    public const TEMPLATE = <<<EOT
<h2>{{ {{ title }} }}</h2>
EOT;
}
