<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output\Element;

/**
 * @internal
 */
final class Amount implements OutputElementInterface
{
    private string $amount;

    public static function create(string $amount): self
    {
        $element = new self();
        $element->amount = $amount;

        return $element;
    }

    public function getText(): string
    {
        return $this->amount;
    }
}
