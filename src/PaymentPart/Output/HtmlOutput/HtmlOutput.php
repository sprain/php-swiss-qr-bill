<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput;

use Sprain\SwissQrBill\PaymentPart\Output\AbstractMarkupOutput;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template\NewlineElementTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template\PlaceholderElementTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template\PrintableBordersTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template\TextElementTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template\PaymentPartTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template\TitleElementReceiptTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template\TitleElementTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template\HideInPrintableTemplate;

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
    public function getPrintableBordersTemplate(): string
    {
        return PrintableBordersTemplate::TEMPLATE;
    }

    /**
     * @return string
     */
    public function getHideInPrintableTemplate(): string
    {
        return HideInPrintableTemplate::TEMPLATE;
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
