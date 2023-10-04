<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template;

class FurtherInformationElementTemplate
{
    public const TEMPLATE = <<<EOT
<p>{{ text }}</p>
EOT;
}
