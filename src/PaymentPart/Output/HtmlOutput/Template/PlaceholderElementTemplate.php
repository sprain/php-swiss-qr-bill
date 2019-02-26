<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template;

class PlaceholderElementTemplate
{
    public const TEMPLATE = <<<EOT
<img src="{{ file }}" style="width:{{ width }}mm; height:{{ height }}mm;" id="{{ id }}">
EOT;
}
