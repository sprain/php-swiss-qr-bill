<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput;

use Sprain\SwissQrBill\PaymentPart\Output\AbstractOutput;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Placeholder;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Text;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Title;
use Sprain\SwissQrBill\PaymentPart\Output\OutputInterface;
use Sprain\SwissQrBill\PaymentPart\Translation\Translation;

abstract class AbstractMarkupOutput extends AbstractOutput implements OutputInterface
{
    abstract function getPaymentPartTemplate(): string;
    abstract function getPlaceholderElementTemplate(): string;
    abstract function getPrintableStylesTemplate(): string;
    abstract function getTextElementTemplate(): string;
    abstract function getTitleElementTemplate(): string;

    /**
     * @return string
     */
    public function getPaymentPart(): string
    {
        $paymentPart = $this->getPaymentPartTemplate();

        $paymentPart = $this->addSwissQrCodeImage($paymentPart);
        $paymentPart = $this->addInformationContent($paymentPart);
        $paymentPart = $this->addInformationContentReceipt($paymentPart);
        $paymentPart = $this->addCurrencyContent($paymentPart);
        $paymentPart = $this->addAmountContent($paymentPart);
        $paymentPart = $this->addAmountContentReceipt($paymentPart);
        $paymentPart = $this->addFurtherInformationContent($paymentPart);
        $paymentPart = $this->hideSeparatorContentIfPrintable($paymentPart);

        $paymentPart = $this->translateContents($paymentPart, $this->getLanguage());

        return $paymentPart;
    }

    /**
     * @param string $paymentPart
     * @return string
     */
    private function addSwissQrCodeImage(string $paymentPart): string
    {
        $qrCode = $this->getQrCode();
        $paymentPart = str_replace('{{ swiss-qr-image }}', $qrCode->writeDataUri(), $paymentPart);

        return $paymentPart;
    }

    /**
     * @param string $paymentPart
     * @return string
     */
    private function addInformationContent(string $paymentPart): string
    {
        $informationContent = '';

        foreach ($this->getInformationElements() as $informationElement) {
            $informationContentPart = $this->getContentElement($informationElement);
            $informationContent .= $informationContentPart;
        }

        $paymentPart = str_replace('{{ information-content }}', $informationContent, $paymentPart);

        return $paymentPart;
    }

    /**
     * @param string $paymentPart
     * @return string
     */
    private function addInformationContentReceipt(string $paymentPart): string
    {
        $informationContent = '';

        foreach ($this->getInformationElementsOfReceipt() as $informationElement) {
            $informationContent .= $this->getContentElement($informationElement);
        }

        $paymentPart = str_replace('{{ information-content-receipt }}', $informationContent, $paymentPart);

        return $paymentPart;
    }

    /**
     * @param string $paymentPart
     * @return string
     */
    private function addCurrencyContent(string $paymentPart): string
    {
        $currencyContent = '';

        foreach ($this->getCurrencyElements() as $currencyElement) {
            $currencyContent .= $this->getContentElement($currencyElement);
        }

        $paymentPart = str_replace('{{ currency-content }}', $currencyContent, $paymentPart);

        return $paymentPart;
    }

    /**
     * @param string $paymentPart
     * @return string
     */
    private function addAmountContent(string $paymentPart): string
    {
        $amountContent = '';

        foreach ($this->getAmountElements() as $amountElement) {
            $amountContent .= $this->getContentElement($amountElement);
        }

        $paymentPart = str_replace('{{ amount-content }}', $amountContent, $paymentPart);

        return $paymentPart;
    }

    /**
     * @param string $paymentPart
     * @return string
     */
    private function addAmountContentReceipt(string $paymentPart): string
    {
        $amountContent = '';

        foreach ($this->getAmountElementsReceipt() as $amountElement) {
            $amountContent .= $this->getContentElement($amountElement);
        }

        $paymentPart = str_replace('{{ amount-content-receipt }}', $amountContent, $paymentPart);

        return $paymentPart;
    }

    /**
     * @param string $paymentPart
     * @return string
     */
    private function addFurtherInformationContent(string $paymentPart): string
    {
        $furtherInformationContent = '';

        foreach ($this->getFurtherInformationElements() as $furtherInformationElement) {
            $furtherInformationContent .= $this->getContentElement($furtherInformationElement);
        }

        $paymentPart = str_replace('{{ further-information-content }}', $furtherInformationContent, $paymentPart);

        return $paymentPart;
    }

    /**
     * @param string $paymentPart
     * @return string
     */
    private function hideSeparatorContentIfPrintable(string $paymentPart): string
    {
        $printableStyles = '';
        if ($this->isPrintable()) {
            $printableStyles = $this->getPrintableStylesTemplate();
        }

        $paymentPart = str_replace('{{ printable-content }}', $printableStyles, $paymentPart);

        return $paymentPart;
    }

    /**
     * @param Title|Text|Placeholder $element Instance of OutputElementInterface.
     * @return string
     */
    private function getContentElement($element): string
    {
        if ($element instanceof Title) {
            $elementTemplate = $this->getTitleElementTemplate();
            $elementString = str_replace('{{ title }}', $element->getTitle(), $elementTemplate);

            return $elementString;
        }

        if ($element instanceof Text) {
            $elementTemplate = $this->getTextElementTemplate();
            $elementString = str_replace('{{ text }}', nl2br($element->getText()), $elementTemplate);

            return $elementString;
        }

        if ($element instanceof Placeholder) {
            $elementTemplate = $this->getPlaceholderElementTemplate();
            $elementString = $elementTemplate;

            $svgDoc = new \DOMDocument();
            $svgDoc->loadXML(file_get_contents($element->getFile()));
            $svg = $svgDoc->getElementsByTagName('svg');
            $dataUri = 'data:image/svg+xml;base64,' . base64_encode($svg->item(0)->C14N());

            $elementString = str_replace('{{ file }}', $dataUri, $elementString);
            $elementString = str_replace('{{ width }}', (string) $element->getWidth(), $elementString);
            $elementString = str_replace('{{ height }}', (string) $element->getHeight(), $elementString);
            $elementString = str_replace('{{ id }}', $element->getType(), $elementString);

            return $elementString;
        }
    }

    /**
     * @param string $paymentPart
     * @param string $language
     * @return string
     */
    private function translateContents(string $paymentPart, string $language): string
    {
        $translations = Translation::getAllByLanguage($language);
        foreach ($translations as $key => $text) {
            $paymentPart = str_replace('{{ text.' . $key . ' }}', $text, $paymentPart);
        }

        return $paymentPart;
    }
}
