<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output;

use Sprain\SwissQrBill\DataGroup\Element\PaymentReference;
use Sprain\SwissQrBill\PaymentPart\Output\Element\FurtherInformation;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Placeholder;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Text;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Title;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;

abstract class AbstractOutput implements OutputInterface
{
    protected DisplayOptions $displayOptions;
    protected string $qrCodeImageFormat;

    public function __construct(protected QrBill $qrBill, protected string $language)
    {
        $this->displayOptions = new DisplayOptions();
        $this->qrCodeImageFormat = QrCode::FILE_FORMAT_SVG;
    }

    public function getQrBill(): ?QrBill
    {
        return $this->qrBill;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * @deprecated Will be removed in next major release. Use setDisplayOptions() instead.
     */
    public function setPrintable(bool $printable): static
    {
        $this->displayOptions->setPrintable($printable);

        return $this;
    }

    /**
     * @deprecated Will be removed in next major release. Use getDisplayOptions() instead.
     */
    public function isPrintable(): bool
    {
        return $this->displayOptions->isPrintable();
    }

    public function setDisplayOptions(DisplayOptions $displayOptions): static
    {
        $this->displayOptions = $displayOptions;

        return $this;
    }

    public function getDisplayOptions(): DisplayOptions
    {
        $this->displayOptions->consolidate();

        return $this->displayOptions;
    }

    public function setQrCodeImageFormat(string $fileExtension): static
    {
        $this->qrCodeImageFormat = $fileExtension;

        return $this;
    }

    public function getQrCodeImageFormat(): string
    {
        return $this->qrCodeImageFormat;
    }

    /**
     * @return list<Title|Text|Placeholder>
     */
    protected function getInformationElements(): array
    {
        $informationElements = [];

        $informationElements[] = Title::create('text.creditor');
        $informationElements[] = Text::create($this->qrBill->getCreditorInformation()->getFormattedIban() . "\n" . $this->qrBill->getCreditor()->getFullAddress());

        if ($this->qrBill->getPaymentReference()->getType() !== PaymentReference::TYPE_NON) {
            $informationElements[] = Title::create('text.reference');
            $informationElements[] = Text::create($this->qrBill->getPaymentReference()->getFormattedReference());
        }

        if ($this->qrBill->getAdditionalInformation()) {
            $informationElements[] = Title::create('text.additionalInformation');
            $informationElements[] = Text::create($this->qrBill->getAdditionalInformation()->getFormattedString());
        }

        if ($this->qrBill->getUltimateDebtor()) {
            $informationElements[] = Title::create('text.payableBy');
            $informationElements[] = Text::create($this->qrBill->getUltimateDebtor()->getFullAddress());
        } else {
            $informationElements[] = Title::create('text.payableByName');
            $informationElements[] = Placeholder::create(Placeholder::PLACEHOLDER_TYPE_PAYABLE_BY);
        }

        return $informationElements;
    }

    /**
     * @return list<Title|Text|Placeholder>
     */
    protected function getInformationElementsOfReceipt(): array
    {
        $informationElements = [];

        $informationElements[] = Title::create('text.creditor');
        $informationElements[] = Text::create($this->qrBill->getCreditorInformation()->getFormattedIban() . "\n" . $this->qrBill->getCreditor()->getFullAddress(true));

        if ($this->qrBill->getPaymentReference()->getType() !== PaymentReference::TYPE_NON) {
            $informationElements[] = Title::create('text.reference');
            $informationElements[] = Text::create($this->qrBill->getPaymentReference()->getFormattedReference());
        }

        if ($this->qrBill->getUltimateDebtor()) {
            $informationElements[] = Title::create('text.payableBy');
            $informationElements[] = Text::create($this->qrBill->getUltimateDebtor()->getFullAddress(true));
        } else {
            $informationElements[] = Title::create('text.payableByName');
            $informationElements[] = Placeholder::create(Placeholder::PLACEHOLDER_TYPE_PAYABLE_BY_RECEIPT);
        }

        return $informationElements;
    }

    /**
     * @return list<Title|Text>
     */
    protected function getCurrencyElements(): array
    {
        $currencyElements = [];

        $currencyElements[] = Title::create('text.currency');
        $currencyElements[] = Text::create($this->qrBill->getPaymentAmountInformation()->getCurrency());

        return $currencyElements;
    }

    /**
     * @return list<Title|Text|Placeholder>
     */
    protected function getAmountElements(): array
    {
        $amountElements = [];

        $amountElements[] = Title::create('text.amount');

        if (null === $this->qrBill->getPaymentAmountInformation()->getAmount()) {
            $amountElements[] = Placeholder::create(Placeholder::PLACEHOLDER_TYPE_AMOUNT);
        } else {
            $amountElements[] = Text::create($this->qrBill->getPaymentAmountInformation()->getFormattedAmount());
        }

        return $amountElements;
    }

    /**
     * @return list<Title|Text|Placeholder>
     */
    protected function getAmountElementsReceipt(): array
    {
        $amountElements = [];

        $amountElements[] = Title::create('text.amount');

        if (null === $this->qrBill->getPaymentAmountInformation()->getAmount()) {
            $amountElements[] = Placeholder::create(Placeholder::PLACEHOLDER_TYPE_AMOUNT_RECEIPT);
        } else {
            $amountElements[] = Text::create($this->qrBill->getPaymentAmountInformation()->getFormattedAmount());
        }

        return $amountElements;
    }

    /**
     * @return list<FurtherInformation>
     */
    protected function getFurtherInformationElements(): array
    {
        $furtherInformationElements = [];

        $furtherInformationLines= [];
        foreach ($this->qrBill->getAlternativeSchemes() as $alternativeScheme) {
            $furtherInformationLines[] = $alternativeScheme->getParameter();
        }
        $furtherInformationElements[] = FurtherInformation::create(implode("\n", $furtherInformationLines));

        return $furtherInformationElements;
    }

    protected function getQrCode(): QrCode
    {
        return $this->qrBill->getQrCode($this->getQrCodeImageFormat());
    }
}
