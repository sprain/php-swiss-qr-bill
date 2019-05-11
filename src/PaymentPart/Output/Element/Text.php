<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\Element;

class Text implements OutputElementInterface
{
    private $text;

    public static function create(string $text): self
    {
        $element = new self();
        $element->text = $text;

        return $element;
    }

    public function getText(): ?string
    {
        return $this->text;
    }
}