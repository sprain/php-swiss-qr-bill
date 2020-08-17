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
