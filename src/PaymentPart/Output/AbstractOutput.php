<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output;

use Sprain\SwissQrBill\DataGroup\Element\PaymentReference;
use Sprain\SwissQrBill\PaymentPart\Output\Element\FurtherInformation;
use Sprain\SwissQrBill\PaymentPart\Output\Element\OutputElementInterface;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Placeholder;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Text;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Title;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;

abstract class AbstractOutput implements OutputInterface
{
    protected QrBill $qrBill;
    protected string $language;
    protected bool $printable;
    protected bool $scissors;
    protected string $qrCodeImageFormat;

    public function __construct(QrBill $qrBill, string $language)
    {
        $this->qrBill = $qrBill;
        $this->language = $language;
        $this->printable = false;
        $this->scissors = false;
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

    public function setPrintable(bool $printable): static
    {
        $this->printable = $printable;

        return $this;
    }

    public function setScissors(bool $scissors): static
    {
        $this->scissors = $scissors;

        return $this;
    }

    public function isPrintable(): bool
    {
        return $this->printable;
    }

    public function isScissors(): bool
    {
        return $this->scissors;
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
