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
    public function getPaymentPartTemplate(): string
    {
        return PaymentPartTemplate::TEMPLATE;
    }

    public function getPlaceholderElementTemplate(): string
    {
        return PlaceholderElementTemplate::TEMPLATE;
    }

    public function getPrintableBordersTemplate(): string
    {
        return PrintableBordersTemplate::TEMPLATE;
    }

    public function getHideInPrintableTemplate(): string
    {
        return HideInPrintableTemplate::TEMPLATE;
    }

    public function getTextElementTemplate(): string
    {
        return TextElementTemplate::TEMPLATE;
    }

    public function getTitleElementTemplate(): string
    {
        return TitleElementTemplate::TEMPLATE;
    }

    public function getTitleElementReceiptTemplate(): string
    {
        return TitleElementReceiptTemplate::TEMPLATE;
    }

    public function getNewlineElementTemplate(): string
    {
        return NewlineElementTemplate::TEMPLATE;
    }
}
