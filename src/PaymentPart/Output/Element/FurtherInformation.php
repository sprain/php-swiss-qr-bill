<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output\Element;

/**
 * @internal
 */
final class FurtherInformation implements OutputElementInterface
{
    private string $furtherInformation;

    public static function create(string $furtherInformation): self
    {
        $element = new self();
        $element->furtherInformation = $furtherInformation;

        return $element;
    }

    public function getText(): string
    {
        return $this->furtherInformation;
    }
}
