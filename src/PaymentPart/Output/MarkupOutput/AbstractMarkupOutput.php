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
    abstract function getTitleElementReceiptTemplate(): string;
    abstract function getNewlineElementTemplate(): string;

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
        $paymentPart = $this->addCurrencyContentReceipt($paymentPart);
        $paymentPart = $this->addAmountContent($paymentPart);
        $paymentPart = $this->addAmountContentReceipt($paymentPart);
        $paymentPart = $this->addFurtherInformationContent($paymentPart);
        $paymentPart = $this->addSeparatorContentIfNotPrintable($paymentPart);

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
            $informationContent .= $this->getContentElement($informationElement, true);
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
    private function addCurrencyContentReceipt(string $paymentPart): string
    {
        $currencyContent = '';

        foreach ($this->getCurrencyElements() as $currencyElement) {
            $currencyContent .= $this->getContentElement($currencyElement, true);
        }

        $paymentPart = str_replace('{{ currency-content-receipt }}', $currencyContent, $paymentPart);

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
            $amountContent .= $this->getContentElement($amountElement, true);
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
    private function addSeparatorContentIfNotPrintable(string $paymentPart): string
    {
        $printableStyles = '';
        if (true !== $this->isPrintable()) {
            $printableStyles = $this->getPrintableStylesTemplate();
        }

        $paymentPart = str_replace('{{ printable-content }}', $printableStyles, $paymentPart);

        return $paymentPart;
    }

    /**
     * @param Title|Text|Placeholder $element Instance of OutputElementInterface.
     * @param bool $isReceiptPart
     * @return string
     */
    private function getContentElement($element, bool $isReceiptPart = false): string
    {
        if ($element instanceof Title) {
            $elementTemplate = $this->getTitleElementTemplate();
            if(true === $isReceiptPart){
                $elementTemplate = $this->getTitleElementReceiptTemplate();
            }
            $elementString = str_replace('{{ title }}', $element->getTitle(), $elementTemplate);

            return $elementString;
        }

        if ($element instanceof Text) {
            $elementTemplate = $this->getTextElementTemplate();
            $elementTextString = str_replace(array("\r\n", "\r", "\n"), $this->getNewlineElementTemplate(), $element->getText());
            $elementString = str_replace('{{ text }}', $elementTextString, $elementTemplate);

            return $elementString;
        }

        if ($element instanceof Placeholder) {
            $elementTemplate = $this->getPlaceholderElementTemplate();
            $elementString = $elementTemplate;

            $dataUri = 'data:image/png;base64,' .  base64_encode(file_get_contents($element->getFile(Placeholder::FILE_TYPE_PNG)));

            // The svg version works but the images have empty space on top and bottom which makes it unnecessary hard to correctly place them.
//            $svgDoc = new \DOMDocument();
//            $svgDoc->loadXML(file_get_contents($element->getFile(Placeholder::FILE_TYPE_SVG))); // Take the png version since the svgs have a bad
//            $svg = $svgDoc->getElementsByTagName('svg');
//            $dataUri = 'data:image/svg+xml;base64,' . base64_encode($svg->item(0)->C14N());

            $elementString = str_replace('{{ placeholder-file }}', $dataUri, $elementString);
            $elementString = str_replace('{{ placeholder-width }}', (string) $element->getWidth(), $elementString);
            $elementString = str_replace('{{ placeholder-height }}', (string) $element->getHeight(), $elementString);
            $elementString = str_replace('{{ placeholder-id }}', $element->getType(), $elementString);
            $elementString = str_replace('{{ placeholder-float }}', $element->getFloat(), $elementString);
            $elementString = str_replace('{{ placeholder-margin-top }}', $element->getMarginTop(), $elementString);

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

            if('separate' === $key && true === $this->isPrintable()){
                // Do not display the separator text at all when printable is true.
                $paymentPart = str_replace('{{ text.' . $key . ' }}', '', $paymentPart);
                continue;
            }

            $paymentPart = str_replace('{{ text.' . $key . ' }}', $text, $paymentPart);
        }

        return $paymentPart;
    }
}
