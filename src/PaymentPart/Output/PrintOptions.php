<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output;

enum LineStyle
{
    case SOLID;
    case DASHED;
    case NONE;
}

enum VerticalSeparatorSymbolPosition
{
    case TOP;
    case BOTTOM;
}

final class PrintOptions
{
    private bool $printable = false;
    private bool $separatorSymbol = false;
    private VerticalSeparatorSymbolPosition $verticalSeparatorSymbolPosition = VerticalSeparatorSymbolPosition::TOP;
    private bool $textDownArrows = false;
    private LineStyle $lineStyle = LineStyle::SOLID;
    private bool $text = true;

    public function isPrintable(): bool
    {
        return $this->printable;
    }

    public function setPrintable(bool $value): static
    {
        $this->printable = $value;

        if (!$this->printable) {
            $this->lineStyle = $this->separatorSymbol ? LineStyle::DASHED : LineStyle::SOLID;
        } else {
            $this->lineStyle = LineStyle::NONE;
        }
        return $this;
    }

    public function hasSeparatorSymbol(): bool
    {
        return $this->separatorSymbol;
    }

    public function setSeparatorSymbol(bool $value): static
    {
        $this->separatorSymbol = $value;
        if (!$this->printable) {
            $this->lineStyle = $this->separatorSymbol ? LineStyle::DASHED : LineStyle::SOLID;
        }
        return $this;
    }

    public function hasTextDownArrows(): bool
    {
        return $this->textDownArrows;
    }

    public function setTextDownArrows(bool $value): static
    {
        $this->textDownArrows = $value;
        return $this;
    }

    public function hasText(): bool
    {
        return $this->text;
    }

    public function setText(bool $value): static
    {
        $this->text = $value;
        return $this;
    }

    public function getVerticalSeparatorSymbolPosition(): VerticalSeparatorSymbolPosition
    {
        return $this->verticalSeparatorSymbolPosition;
    }

    public function setVerticalSeparatorSymbolPosition(VerticalSeparatorSymbolPosition $value): static
    {
        $this->verticalSeparatorSymbolPosition = $value;
        return $this;
    }

    public function getLineStyle(): LineStyle
    {
        return $this->lineStyle;
    }
}
