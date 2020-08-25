<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template;

class PlaceholderElementTemplate
{
    public const TEMPLATE = <<<EOT
<img src="{{ placeholder-file }}" style="width:{{ placeholder-width }}mm; height:{{ placeholder-height }}mm; float:{{ placeholder-float }}" class="qr-bill-placeholder" id="{{ placeholder-id }}">
EOT;
}
