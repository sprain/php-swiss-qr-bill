<?php

namespace Sprain\SwissQrBill\PaymentPart\HtmlOutput\Templates;

class ContentElementTemplate
{
    const TEMPLATE = <<<EOT
<h2>{{ title }}</h2>
<p>{{ content }}</p>
EOT;
}
