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
    private bool $isPrintable = false;
    private bool $hasSeparatorSymbol = false;
    private bool $hasTextDownArrows = false;
    private bool $hasText = true;
    private string $verticalSeparatorSymbolPosition = VerticalSeparatorSymbolPosition::TOP;
    private string $lineStyle = LineStyle::SOLID;

    public function isPrintable(): bool
    {
        return $this->isPrintable;
    }

    public function setPrintable(bool $isPrintable): self
    {
        $this->isPrintable = $isPrintable;

        return $this;
    }

    public function hasSeparatorSymbol(): bool
    {
        return $this->hasSeparatorSymbol;
    }

    public function setSeparatorSymbol(bool $hasSeparatorSymbol): self
    {
        $this->hasSeparatorSymbol = $hasSeparatorSymbol;

        return $this;
    }

    public function hasTextDownArrows(): bool
    {
        return $this->hasTextDownArrows;
    }

    public function setTextDownArrows(bool $hasTextDownArrows): self
    {
        $this->hasTextDownArrows = $hasTextDownArrows;

        return $this;
    }

    public function hasText(): bool
    {
        return $this->hasText;
    }

    public function setText(bool $hasText): self
    {
        $this->hasText = $hasText;

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
        $this->lineStyle = $this->hasSeparatorSymbol ? LineStyle::DASHED : LineStyle::SOLID;

        if ($this->isPrintable) {
            $this->lineStyle = LineStyle::NONE;
        }

        if ($this->hasSeparatorSymbol) {
            $this->hasText = false;
            $this->hasTextDownArrows = false;
        }
    }
}
