<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\Html;

class PlaceholderElementTemplate
{
    public const TEMPLATE = <<<EOT
<img src="{{ file }}" style="width:{{ width }}mm; height:{{ height }}mm;" class="qr-bill-placeholder" id="{{ id }}">
EOT;
}
