<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output;

final class DisplayOptions
{
    private bool $isPrintable = false;
    private bool $displayScissors = false;
    private bool $positionScissorsAtBottom = false;
    private bool $displayTextDownArrows = false;
    private bool $displayText = true;
    private LineStyle $lineStyle = LineStyle::SOLID;

    public function isPrintable(): bool
    {
        return $this->isPrintable;
    }

    public function setPrintable(bool $isPrintable): self
    {
        $this->isPrintable = $isPrintable;

        return $this;
    }

    public function isDisplayScissors(): bool
    {
        return $this->displayScissors;
    }

    public function setDisplayScissors(bool $displayScissors): self
    {
        $this->displayScissors = $displayScissors;

        return $this;
    }

    public function isPositionScissorsAtBottom(): bool
    {
        return $this->positionScissorsAtBottom;
    }

    public function setPositionScissorsAtBottom(bool $positionScissorsAtBottom): self
    {
        $this->positionScissorsAtBottom = $positionScissorsAtBottom;

        return $this;
    }

    public function isDisplayTextDownArrows(): bool
    {
        return $this->displayTextDownArrows;
    }

    public function setDisplayTextDownArrows(bool $displayTextDownArrows): self
    {
        $this->displayTextDownArrows = $displayTextDownArrows;

        return $this;
    }

    public function isDisplayText(): bool
    {
        return $this->displayText;
    }

    public function getLineStyle(): LineStyle
    {
        return $this->lineStyle;
    }

    /**
     * @internal
     */
    public function consolidate(): void
    {
        $this->lineStyle = $this->displayScissors ? LineStyle::DASHED : LineStyle::SOLID;

        if ($this->isPrintable) {
            $this->lineStyle = LineStyle::NONE;
        }

        if ($this->displayScissors) {
            $this->displayText = false;
            $this->displayTextDownArrows = false;
        }
    }
}

/**
 * @internal
 */
enum LineStyle
{
    case SOLID;
    case DASHED;
    case NONE;
}
