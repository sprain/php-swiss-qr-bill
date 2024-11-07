<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output;

/**
 * @internal
 */
final class LineStyle
{
    public const SOLID = 'SOLID';
    public const DASHED = 'DASHED';
    public const NONE = 'NONE';
}

/**
 * @internal
 */
final class VerticalSeparatorSymbolPosition
{
    public const TOP = 'TOP';
    public const BOTTOM = 'BOTTOM';
}

final class PrintOptions
{
    private bool $printable = false;
    private bool $separatorSymbol = false;
    private string $verticalSeparatorSymbolPosition = VerticalSeparatorSymbolPosition::TOP;
    private bool $textDownArrows = false;
    private string $lineStyle = LineStyle::SOLID;
    private bool $text = true;

    public function isPrintable(): bool
    {
        return $this->printable;
    }

    public function setPrintable(bool $isPrintable): self
    {
        $this->printable = $isPrintable;

        return $this;
    }

    public function hasSeparatorSymbol(): bool
    {
        return $this->separatorSymbol;
    }

    public function setSeparatorSymbol(bool $hasSeparatorSymbol): self
    {
        $this->separatorSymbol = $hasSeparatorSymbol;

        return $this;
    }

    public function hasTextDownArrows(): bool
    {
        return $this->textDownArrows;
    }

    public function setTextDownArrows(bool $hasTextDownArrows): self
    {
        $this->textDownArrows = $hasTextDownArrows;

        return $this;
    }

    public function hasText(): bool
    {
        return $this->text;
    }

    public function setText(bool $hasText): self
    {
        $this->text = $hasText;

        return $this;
    }

    public function getVerticalSeparatorSymbolPosition(): string
    {
        return $this->verticalSeparatorSymbolPosition;
    }

    public function setVerticalSeparatorSymbolPosition(string $verticalSeparatorSymbolPosition): self
    {
        $this->verticalSeparatorSymbolPosition = $verticalSeparatorSymbolPosition;

        return $this;
    }

    public function getLineStyle(): string
    {
        return $this->lineStyle;
    }

    /**
     * @internal
     */
    public function consolidate(): void
    {
        $this->lineStyle = $this->separatorSymbol ? LineStyle::DASHED : LineStyle::SOLID;

        if ($this->printable) {
            $this->lineStyle = LineStyle::NONE;
        }

        if ($this->separatorSymbol) {
            $this->text = false;
            $this->textDownArrows = false;
        }
    }
}
