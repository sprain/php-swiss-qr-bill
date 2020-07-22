<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput;

use Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\Html\PlaceholderElementTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\Html\PrintableStylesTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\Html\TextElementTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\Html\PaymentPartTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\Html\TitleElementTemplate;

final class HtmlOutput extends AbstractMarkupOutput
{
    /**
     * @return string
     */
    function getPaymentPartTemplate(): string
    {
        return PaymentPartTemplate::TEMPLATE;
    }

    /**
     * @return string
     */
    function getPlaceholderElementTemplate(): string
    {
        return PlaceholderElementTemplate::TEMPLATE;
    }

    /**
     * @return string
     */
    function getPrintableStylesTemplate(): string
    {
        return PrintableStylesTemplate::TEMPLATE;
    }

    /**
     * @return string
     */
    function getTextElementTemplate(): string
    {
        return TextElementTemplate::TEMPLATE;
    }

    /**
     * @return string
     */
    function getTitleElementTemplate(): string
    {
        return TitleElementTemplate::TEMPLATE;
    }
}
