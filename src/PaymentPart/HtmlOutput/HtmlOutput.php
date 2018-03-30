<?php

namespace Sprain\SwissQrBill\PaymentPart\HtmlOutput;

use Sprain\SwissQrBill\PaymentPart\HtmlOutput\Templates\ContentElementTemplate;
use Sprain\SwissQrBill\PaymentPart\HtmlOutput\Templates\PaymentPartTemplate;
use Sprain\SwissQrBill\PaymentPart\AbstractOutput;
use Sprain\SwissQrBill\PaymentPart\Translation\Translation;

class HtmlOutput extends AbstractOutput
{
    public function getPaymentPart() : string
    {
        $paymentPart = PaymentPartTemplate::TEMPLATE;

        $paymentPart = $this->addSchemeContent($paymentPart);
        $paymentPart = $this->addSwissQrCodeImage($paymentPart);
        $paymentPart = $this->addInformationContent($paymentPart);
        $paymentPart = $this->addCurrencyContent($paymentPart);
        $paymentPart = $this->addAmountContent($paymentPart);

        $paymentPart = $this->translateContents($paymentPart, $this->getLanguage());

        return $paymentPart;
    }

    protected function addSchemeContent(string $paymentPart) : string
    {
        $schemeContent = $this->getContentElement('{{ text.supports }}', '{{ text.creditTransfer }}');
        $paymentPart = str_replace('{{ scheme-content }}', $schemeContent, $paymentPart);

        return $paymentPart;
    }

    protected function addSwissQrCodeImage(string $paymentPart) : string
    {
        $paymentPart = str_replace('{{ swiss-qr-image }}', $this->qrBill->getQrCode()->writeDataUri(), $paymentPart);

        return $paymentPart;
    }

    protected function addInformationContent(string $paymentPart) : string
    {
        $informationContent = '';

        foreach($this->getInformationElements() as $key => $content) {
            $informationContentPart = $this->getContentElement('{{ '.$key.' }}', $content);
            $informationContent .= $informationContentPart;
        }

        $paymentPart = str_replace('{{ information-content }}', $informationContent, $paymentPart);

        return $paymentPart;
    }

    protected function addCurrencyContent(string $paymentPart) : string
    {
        $currencyContent = $this->getContentElement('{{ text.currency }}', $this->qrBill->getPaymentAmountInformation()->getCurrency());
        $paymentPart = str_replace('{{ currency-content }}', $currencyContent, $paymentPart);

        return $paymentPart;
    }

    protected function addAmountContent(string $paymentPart) : string
    {
        $amountString = number_format(
            $this->qrBill->getPaymentAmountInformation()->getAmount(),
            2,
            '.',
            ' '
        );

        $amountContent = $this->getContentElement('{{ text.amount }}', $amountString);
        $paymentPart = str_replace('{{ amount-content }}', $amountContent, $paymentPart);

        return $paymentPart;
    }

    protected function getContentElement(string $title, string $content) : string
    {
        $contentElementTemplate = ContentElementTemplate::TEMPLATE;
        $contentElement = $contentElementTemplate;

        $contentElement = str_replace('{{ title }}', $title, $contentElement);
        $contentElement = str_replace('{{ content }}', $content, $contentElement);

        return $contentElement;
    }

    protected function translateContents($paymentPart, $language)
    {
        $translations = Translation::getAllByLanguage($language);
        foreach($translations as $key => $text) {
            $paymentPart = str_replace('{{ text.' . $key . ' }}', $text, $paymentPart);
        }

        return $paymentPart;
    }
}