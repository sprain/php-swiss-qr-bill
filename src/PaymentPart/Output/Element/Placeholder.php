<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\Element;

class Placeholder implements OutputElementInterface
{
    const PLACEHOLDER_TYPE_PAYABLE_BY = [
        'type' => 'placeholder_payable_by',
        'file' => __DIR__ . '/../../../../assets/marks_65x25mm.svg',
        'width' => 65,
        'height' => 25
    ];

    const PLACEHOLDER_TYPE_PAYABLE_BY_RECEIPT = [
        'type' => 'placeholder_payable_by_receipt',
        'file' => __DIR__ . '/../../../../assets/marks_52x20mm.svg',
        'width' => 52,
        'height' => 20
    ];

    const PLACEHOLDER_TYPE_AMOUNT = [
        'type' => 'placeholder_amount',
        'file' => __DIR__ . '/../../../../assets/marks_40x15mm.svg',
        'width' => 40,
        'height' => 15
    ];

    const PLACEHOLDER_TYPE_AMOUNT_RECEIPT = [
        'type' => 'placeholder_amount_receipt',
        'file' => __DIR__ . '/../../../../assets/marks_30x10mm.svg',
        'width' => 30,
        'height' => 10
    ];

    /** @var string */
    private $type;

    /** @var string */
    private $file;

    /** @var int */
    private $width;

    /** @var int */
    private $height;

    public static function create(array $type): self
    {
        $placeholder = new self();
        $placeholder->type = $type['type'];
        $placeholder->file = $type['file'];
        $placeholder->width = $type['width'];
        $placeholder->height = $type['height'];

        return $placeholder;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    // TODO: Not tested, need to create an example_non to test Placeholders
    public function getFile(?string $ext = 'svg'): ?string
    {
        if ($ext === 'png') {
            $this->file = str_replace('.svg', '.png', $this->file);
        }
        return $this->file;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }
}
