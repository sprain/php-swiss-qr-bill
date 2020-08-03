<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\XslFo;

class PlaceholderElementTemplate
{
    public const TEMPLATE = <<<EOT
    <fo:block margin-top="{{ placeholder-margin-top }}mm" text-align="{{ placeholder-float }}">
        <fo:external-graphic vertical-align="top" id="{{ placeholder-id }}" src="{{ placeholder-file }}" height="{{ placeholder-height }}mm" width="{{ placeholder-width }}mm" content-width="scale-to-fit"/>
    </fo:block>
EOT;
}
