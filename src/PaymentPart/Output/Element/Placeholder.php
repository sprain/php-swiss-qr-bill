<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\Element;

class Placeholder implements OutputElementInterface
{
    public const FILE_TYPE_SVG = 'svg';
    public const FILE_TYPE_PNG = 'png';

    public const PLACEHOLDER_TYPE_PAYABLE_BY = [
        'type' => 'placeholder_payable_by',
        'fileSvg' => __DIR__ . '/../../../../assets/marks_65x25mm.svg',
        'filePng' => __DIR__ . '/../../../../assets/marks_65x25mm.png',
        'width' => 65,
        'height' => 25,
        'float' => 'left',
        'marginTop' => 0, // In mm.
    ];

    public const PLACEHOLDER_TYPE_PAYABLE_BY_RECEIPT = [
        'type' => 'placeholder_payable_by_receipt',
        'fileSvg' => __DIR__ . '/../../../../assets/marks_52x20mm.svg',
        'filePng' => __DIR__ . '/../../../../assets/marks_52x20mm.png',
        'width' => 52,
        'height' => 20,
        'float' => 'right',
        'marginTop' => 0, // In mm.
    ];

    public const PLACEHOLDER_TYPE_AMOUNT = [
        'type' => 'placeholder_amount',
        'fileSvg' => __DIR__ . '/../../../../assets/marks_40x15mm.svg',
        'filePng' => __DIR__ . '/../../../../assets/marks_40x15mm.png',
        'width' => 40,
        'height' => 15,
        'float' => 'right',
        'marginTop' => -0.9 // In mm.
    ];

    public const PLACEHOLDER_TYPE_AMOUNT_RECEIPT = [
        'type' => 'placeholder_amount_receipt',
        'fileSvg' => __DIR__ . '/../../../../assets/marks_30x10mm.svg',
        'filePng' => __DIR__ . '/../../../../assets/marks_30x10mm.png',
        'width' => 30,
        'height' => 10,
        'float' => 'right',
        'marginTop' => -3.175 // In mm.
    ];

    /** @var string */
    private $type;

    /** @var string */
    private $fileSvg;

    /** @var string */
    private $filePng;

    /** @var int */
    private $width;

    /** @var int */
    private $height;

    /** @var string */
    private $float;

    /** @var int */
    private $marginTop;

    public static function create(array $type): self
    {
        $placeholder = new self();
        $placeholder->type = $type['type'];
        $placeholder->fileSvg = $type['fileSvg'];
        $placeholder->filePng = $type['filePng'];
        $placeholder->width = $type['width'];
        $placeholder->height = $type['height'];
        $placeholder->float = $type['float'];
        $placeholder->marginTop = $type['marginTop'];

        return $placeholder;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getFile($type = self::FILE_TYPE_SVG): string
    {
        switch ($type) {
            case self::FILE_TYPE_PNG:
                return $this->filePng;
            case self::FILE_TYPE_SVG:
            default:
                return $this->fileSvg;
        }
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function getFloat(): ?string
    {
        return $this->float;
    }

    public function getMarginTop(): float
    {
        return $this->marginTop ?: 0;
    }
}
