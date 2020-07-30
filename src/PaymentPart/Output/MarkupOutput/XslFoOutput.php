<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput;

use Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\XslFo\PlaceholderElementTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\XslFo\PrintableStylesTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\XslFo\TextElementTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\XslFo\PaymentPartTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\XslFo\TitleElementTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\XslFo\TitleElementReceiptTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\XslFo\NewlineElementTemplate;

final class XslFoOutput extends AbstractMarkupOutput
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

    /**
     * @return string
     */
    function getTitleElementReceiptTemplate(): string
    {
        return TitleElementReceiptTemplate::TEMPLATE;
    }

    /**
     * @return string
     */
    function getNewlineElementTemplate(): string
    {
        return NewlineElementTemplate::TEMPLATE;
    }
}
