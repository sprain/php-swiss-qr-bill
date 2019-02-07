<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template;

class ContentElementTemplate
{
    public const TEMPLATE = <<<EOT
<h2>{{ title }}</h2>
<p>{{ content }}</p>
EOT;
}
