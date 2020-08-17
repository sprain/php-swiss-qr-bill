<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput;

use Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\Html\NewlineElementTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\Html\PlaceholderElementTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\Html\PrintableStylesTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\Html\TextElementTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\Html\PaymentPartTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\Html\TitleElementReceiptTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\Html\TitleElementTemplate;

final class HtmlOutput extends AbstractMarkupOutput
{
    /**
     * @return string
     */
    public function getPaymentPartTemplate(): string
    {
        return PaymentPartTemplate::TEMPLATE;
    }

    /**
     * @return string
     */
    public function getPlaceholderElementTemplate(): string
    {
        return PlaceholderElementTemplate::TEMPLATE;
    }

    /**
     * @return string
     */
    public function getPrintableStylesTemplate(): string
    {
        return PrintableStylesTemplate::TEMPLATE;
    }

    /**
     * @return string
     */
    public function getTextElementTemplate(): string
    {
        return TextElementTemplate::TEMPLATE;
    }

    /**
     * @return string
     */
    public function getTitleElementTemplate(): string
    {
        return TitleElementTemplate::TEMPLATE;
    }

    /**
     * @return string
     */
    public function getTitleElementReceiptTemplate(): string
    {
        return TitleElementReceiptTemplate::TEMPLATE;
    }

    /**
     * @return string
     */
    public function getNewlineElementTemplate(): string
    {
        return NewlineElementTemplate::TEMPLATE;
    }
}
