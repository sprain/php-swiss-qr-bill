<?php

namespace Sprain\SwissQrBill\PaymentPart\HtmlOutput\Template;

class ContentElementTemplate
{
    public const TEMPLATE = <<<EOT
<h2>{{ title }}</h2>
<p>{{ content }}</p>
EOT;
}
