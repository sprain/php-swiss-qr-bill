<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\XslFoOutput;

use Sprain\SwissQrBill\PaymentPart\Output\AbstractMarkupOutput;
use Sprain\SwissQrBill\PaymentPart\Output\XslFoOutput\Template\HideInPrintableTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\XslFoOutput\Template\PlaceholderElementTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\XslFoOutput\Template\PrintableBordersTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\XslFoOutput\Template\TextElementTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\XslFoOutput\Template\PaymentPartTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\XslFoOutput\Template\TitleElementTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\XslFoOutput\Template\TitleElementReceiptTemplate;
use Sprain\SwissQrBill\PaymentPart\Output\XslFoOutput\Template\NewlineElementTemplate;

final class XslFoOutput extends AbstractMarkupOutput
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
