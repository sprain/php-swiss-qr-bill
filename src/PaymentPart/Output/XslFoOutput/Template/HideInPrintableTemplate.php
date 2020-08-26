<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\XslFoOutput\Template;

class HideInPrintableTemplate
{
    // Note: visibility="hidden" would be the preferred attribute here but it does not seem to work for unknown reasons.
    public const TEMPLATE = <<<EOT
color="white"
EOT;
}
