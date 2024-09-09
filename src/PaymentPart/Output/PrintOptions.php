<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output;

enum LineStyles
{
    case SOLID;
    case DASHED;
    case NONE;
}

enum VerticalSeparatorSymbolPositions
{
    case TOP;
    case BOTTOM;
}

enum Fonts
{
    case DEFAULT;
    case UTF8;
}

final class PrintOptions
{
    private bool $printable = false;
    private bool $separatorSymbol = false;
    private VerticalSeparatorSymbolPositions $verticalSeparatorSymbolPosition = VerticalSeparatorSymbolPositions::TOP;
    private bool $textDownArrows = false;
    private LineStyles $lineStyle = LineStyles::SOLID;
    private bool $text = true;
    private string|Fonts $font = Fonts::DEFAULT;

    public function isPrintable(): bool
    {
        return $this->printable;
    }

    public function setPrintable(bool $value): static
    {
        $this->printable = $value;

        if (!$this->printable) {
            $this->lineStyle = $this->separatorSymbol ? LineStyles::DASHED : LineStyles::SOLID;
        } else {
            $this->lineStyle = LineStyles::NONE;
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
            $this->lineStyle = $this->separatorSymbol ? LineStyles::DASHED : LineStyles::SOLID;
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

    public function getVerticalSeparatorSymbolPosition(): VerticalSeparatorSymbolPositions
    {
        return $this->verticalSeparatorSymbolPosition;
    }

    public function setVerticalSeparatorSymbolPosition(VerticalSeparatorSymbolPositions $value): static
    {
        $this->verticalSeparatorSymbolPosition = $value;
        return $this;
    }

    public function getFont(): string|Fonts
    {
        return $this->font;
    }

    public function setFont(string|Fonts $value): static
    {
        $this->font = $value;
        return $this;
    }

    public function getLineStyle(): LineStyles
    {
        return $this->lineStyle;
    }
}
