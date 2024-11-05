<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output;

/**
 * @internal This class is meant to be an ENUM. But because it must be compatible
 * with PHP 8.0, ENUMs should not be used, since they exist only from PHP 8.1
 * The idea here is to have a usage as close as an ENUM.
 *
 * There would be one breaking change for consumers:
 * 1. only an ENUM value could be given to the method setXXX(). Now, standard strings are also accepted
 */
final class LineStyle
{
    public const SOLID = 'SOLID';
    public const DASHED = 'DASHED';
    public const NONE = 'NONE';
}

/**
 * @internal See internal comment for LineStyle above
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
    private VerticalSeparatorSymbolPosition|string $verticalSeparatorSymbolPosition = VerticalSeparatorSymbolPosition::TOP;
    private bool $textDownArrows = false;
    private LineStyle|string $lineStyle = LineStyle::SOLID;
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
        if ($this->separatorSymbol) {
            $this->text = false;
        }
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
        if ($this->text) {
            $this->separatorSymbol = false;
        }
        if (!$this->printable) {
            $this->lineStyle = LineStyle::SOLID;
        }
        return $this;
    }

    public function getVerticalSeparatorSymbolPosition(): VerticalSeparatorSymbolPosition|string
    {
        return $this->verticalSeparatorSymbolPosition;
    }

    public function setVerticalSeparatorSymbolPosition(VerticalSeparatorSymbolPosition|string $value): static
    {
        $this->verticalSeparatorSymbolPosition = $value;
        return $this;
    }

    public function getLineStyle(): LineStyle|string
    {
        return $this->lineStyle;
    }
}
