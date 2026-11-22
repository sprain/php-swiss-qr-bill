<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output\TcLibPdfOutput;

use Com\Tecnick\Pdf\Tcpdf;
use Sprain\SwissQrBill\Exception\InvalidTcLibPdfImageFormat;
use Sprain\SwissQrBill\PaymentPart\Output\AbstractOutput;
use Sprain\SwissQrBill\PaymentPart\Output\Element\FurtherInformation;
use Sprain\SwissQrBill\PaymentPart\Output\Element\OutputElementInterface;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Placeholder;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Text;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Title;
use Sprain\SwissQrBill\PaymentPart\Output\LineStyle;
use Sprain\SwissQrBill\PaymentPart\Translation\Translation;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;

/**
 * Payment part output for tecnickcom/tc-lib-pdf (the modular successor of TCPDF).
 *
 * tc-lib-pdf has no text cursor (SetX/SetY/Cell/Ln). This class therefore keeps its own
 * cursor and writes every line at an absolute position. All positions are defined in
 * millimeters and converted to the unit of the given pdf, so any document unit works.
 * Content is written directly to the page, independent of its margins and regions.
 *
 * Fonts: tc-lib-pdf does not ship any fonts, they are resolved via K_PATH_FONTS.
 * The defaults use the core fonts 'helvetica' and 'helveticab', plus 'zapfdingbats' if
 * scissors or down arrows are displayed. Custom font keys may be passed to the constructor.
 * The core fonts are limited to the characters of Windows-1252, use a unicode font for any other characters.
 */
final class TcLibPdfOutput extends AbstractOutput
{
    private const ALIGN_LEFT = 'L';
    private const ALIGN_RIGHT = 'R';
    private const FONT = 'helvetica';
    private const FONT_SYMBOLS = 'zapfdingbats';
    private const FONT_SYMBOLS_CHAR_SCISSORS = '"';
    private const FONT_SYMBOLS_CHAR_DOWN_ARROW = 't';

    private const POINTS_PER_MM = 72 / 25.4;

    // Ratio of line height to font size
    private const CELL_HEIGHT_RATIO_DEFAULT = 1.25;
    private const LEFT_CELL_HEIGHT_RATIO_COMMON = 1.2;
    private const RIGHT_CELL_HEIGHT_RATIO_COMMON = 1.1;
    private const LEFT_CELL_HEIGHT_RATIO_CURRENCY_AMOUNT = 1.5;
    private const RIGHT_CELL_HEIGHT_RATIO_CURRENCY_AMOUNT = 1.5;
    private const FURTHER_INFORMATION_CELL_HEIGHT_RATIO_COMMON = 1.5;

    // Positioning in mm
    private const PAGE_CENTER_X = 105;
    private const SEPARATOR_X_START = 2;
    private const SEPARATOR_X_MIDDLE = 62;
    private const SEPARATOR_X_END = 208;
    private const SEPARATOR_Y = 193;
    private const SEPARATOR_Y_END = 296;
    private const SEPARATOR_DASH_LENGTH = 0.7;
    private const CURRENCY_AMOUNT_Y = 259;
    private const LEFT_PART_X = 5;
    private const LEFT_PART_X_AMOUNT = 17;
    private const RIGHT_PART_X = 67;
    private const RIGHT_PART_X_AMOUNT = 81;
    private const RIGHT_PART_X_INFO = 118;
    private const TITLE_Y = 195;
    private const TITLE_HEIGHT = 7;

    // Widths in mm
    private const WIDTH_RECEIPT = 52;
    private const WIDTH_PAYMENT_PART_INFO = 87;
    private const WIDTH_FURTHER_INFORMATION = 138;
    private const WIDTH_UNLIMITED = 0;

    // Font sizes in pt
    private const FONT_SIZE_MAIN_TITLE = 11;
    private const FONT_SIZE_TITLE_RECEIPT = 6;
    private const FONT_SIZE_RECEIPT = 8;
    private const FONT_SIZE_TITLE_PAYMENT_PART = 8;
    private const FONT_SIZE_PAYMENT_PART = 10;
    private const FONT_SIZE_FURTHER_INFORMATION = 7;
    private const FONT_SIZE_SCISSORS = 14;
    private const FONT_SIZE_DOWN_ARROW = 10;

    // Spacing after a text element in mm
    private const LINE_SPACING_RECEIPT = 3.5;
    private const LINE_SPACING_PAYMENT_PART = 4.8;

    private int $pageId = -1;
    private float $y = 0;
    private float $fontSize = 0;
    private float $fontAscent = 0;
    private float $fontDescent = 0;
    private float $cellHeightRatio = self::CELL_HEIGHT_RATIO_DEFAULT;

    /**
     * @param string $font        Key of the font for regular text.
     * @param string $fontBold    Key of the font for bold text. If it equals $font, its bold style is used.
     * @param string $fontSymbols Key of a font with ZapfDingbats encoding, used for scissors and down arrows.
     */
    public function __construct(
        QrBill $qrBill,
        string $language,
        private readonly Tcpdf $tcLibPdf,
        private readonly float $offsetX = 0,
        private readonly float $offsetY = 0,
        private readonly string $font = self::FONT,
        private readonly string $fontBold = self::FONT,
        private readonly string $fontSymbols = self::FONT_SYMBOLS
    ) {
        parent::__construct($qrBill, $language);
        $this->setQrCodeImageFormat(QrCode::FILE_FORMAT_PNG);
    }

    public function setQrCodeImageFormat(string $fileExtension): static
    {
        if (QrCode::FILE_FORMAT_SVG === $fileExtension) {
            // tc-lib-pdf drops the image of the swiss cross, which is embedded in the svg, when the svg is passed as a string.
            throw new InvalidTcLibPdfImageFormat('SVG images are not supported by TcLibPdfOutput.');
        }

        $this->qrCodeImageFormat = $fileExtension;

        return $this;
    }

    public function getPaymentPart(): ?string
    {
        $this->pageId = $this->tcLibPdf->page->getPageID();
        $fontStackSize = $this->tcLibPdf->font->getStackSize();

        $this->addContent($this->tcLibPdf->graph->getStartTransform());
        $this->addContent($this->tcLibPdf->graph->getStyleCmd(['fillColor' => 'black']));

        $this->addSeparatorContentIfNotPrintable();

        $this->addInformationContentReceipt();
        $this->addCurrencyContentReceipt();
        $this->addAmountContentReceipt();

        $this->addSwissQrCodeImage();
        $this->addInformationContent();
        $this->addCurrencyContent();
        $this->addAmountContent();
        $this->addFurtherInformationContent();

        $this->addContent($this->tcLibPdf->graph->getStopTransform());

        // Keep the current font of tc-lib-pdf in line with the restored graphics state of the page
        while ($this->tcLibPdf->font->getStackSize() > $fontStackSize) {
            $this->tcLibPdf->font->popLastFont();
        }

        return null;
    }

    private function addSwissQrCodeImage(): void
    {
        $image = $this->getQrCode()->getAsString($this->getQrCodeImageFormat());

        $this->addImage($image, self::RIGHT_PART_X, 209.5, 46, 46);
    }

    private function addInformationContentReceipt(): void
    {
        $this->cellHeightRatio = self::LEFT_CELL_HEIGHT_RATIO_COMMON;

        // Title
        $this->setFont(true, self::FONT_SIZE_MAIN_TITLE);
        $this->y = self::TITLE_Y;
        $this->printLine(Translation::get('receipt', $this->language), self::LEFT_PART_X, self::TITLE_HEIGHT);

        // Elements
        $this->y = 204;
        foreach ($this->getInformationElementsOfReceipt() as $informationElement) {
            $this->setContentElement($informationElement, true, self::LEFT_PART_X, self::WIDTH_RECEIPT);
        }

        // Acceptance section
        $this->setFont(true, self::FONT_SIZE_TITLE_RECEIPT);
        $this->y = 273;
        $this->printLine(
            Translation::get('acceptancePoint', $this->language),
            self::LEFT_PART_X,
            null,
            self::ALIGN_RIGHT,
            self::WIDTH_RECEIPT
        );
    }

    private function addInformationContent(): void
    {
        $this->cellHeightRatio = self::RIGHT_CELL_HEIGHT_RATIO_COMMON;

        // Title
        $this->setFont(true, self::FONT_SIZE_MAIN_TITLE);
        $this->y = self::TITLE_Y;
        $this->printLine(Translation::get('paymentPart', $this->language), self::RIGHT_PART_X, self::TITLE_HEIGHT);

        // Elements
        $this->y = 197;
        foreach ($this->getInformationElements() as $informationElement) {
            $this->setContentElement($informationElement, false, self::RIGHT_PART_X_INFO, self::WIDTH_PAYMENT_PART_INFO);
        }
    }

    private function addCurrencyContentReceipt(): void
    {
        $this->cellHeightRatio = self::LEFT_CELL_HEIGHT_RATIO_CURRENCY_AMOUNT;
        $this->y = self::CURRENCY_AMOUNT_Y;

        foreach ($this->getCurrencyElements() as $currencyElement) {
            $this->setContentElement($currencyElement, true, self::LEFT_PART_X, self::WIDTH_UNLIMITED);
        }
    }

    private function addAmountContentReceipt(): void
    {
        $this->cellHeightRatio = self::LEFT_CELL_HEIGHT_RATIO_CURRENCY_AMOUNT;
        $this->y = self::CURRENCY_AMOUNT_Y;

        foreach ($this->getAmountElementsReceipt() as $amountElement) {
            $this->setContentElement($amountElement, true, self::LEFT_PART_X_AMOUNT, self::WIDTH_UNLIMITED);
        }
    }

    private function addCurrencyContent(): void
    {
        $this->cellHeightRatio = self::RIGHT_CELL_HEIGHT_RATIO_CURRENCY_AMOUNT;
        $this->y = self::CURRENCY_AMOUNT_Y;

        foreach ($this->getCurrencyElements() as $currencyElement) {
            $this->setContentElement($currencyElement, false, self::RIGHT_PART_X, self::WIDTH_UNLIMITED);
        }
    }

    private function addAmountContent(): void
    {
        $this->cellHeightRatio = self::RIGHT_CELL_HEIGHT_RATIO_CURRENCY_AMOUNT;
        $this->y = self::CURRENCY_AMOUNT_Y;

        foreach ($this->getAmountElements() as $amountElement) {
            $this->setContentElement($amountElement, false, self::RIGHT_PART_X_AMOUNT, self::WIDTH_UNLIMITED);
        }
    }

    private function addFurtherInformationContent(): void
    {
        $this->cellHeightRatio = self::FURTHER_INFORMATION_CELL_HEIGHT_RATIO_COMMON;
        $this->y = 286;

        foreach ($this->getFurtherInformationElements() as $furtherInformationElement) {
            $this->setContentElement($furtherInformationElement, false, self::RIGHT_PART_X, self::WIDTH_FURTHER_INFORMATION);
        }
    }

    private function addSeparatorContentIfNotPrintable(): void
    {
        $layout = $this->getDisplayOptions();
        if ($layout->isPrintable()) {
            return;
        }

        if ($layout->getLineStyle() !== LineStyle::NONE) {
            $this->addSeparatorLines($layout->getLineStyle() === LineStyle::DASHED);
        }

        if ($layout->isDisplayScissors()) {
            $this->addScissors($layout->isPositionScissorsAtBottom());
        }

        if ($layout->isDisplayText()) {
            $this->addSeparatorText($layout->isDisplayTextDownArrows());
        }
    }

    private function addSeparatorLines(bool $dashed): void
    {
        $style = [
            'lineWidth' => $this->mm(0.1),
            'lineColor' => 'black',
            'lineCap' => 'butt',
            'dashArray' => $dashed ? [$this->mm(self::SEPARATOR_DASH_LENGTH)] : [],
            'dashPhase' => 0,
        ];

        $lines = [
            [self::SEPARATOR_X_START, self::SEPARATOR_Y, self::SEPARATOR_X_END, self::SEPARATOR_Y],
            [self::SEPARATOR_X_MIDDLE, self::SEPARATOR_Y, self::SEPARATOR_X_MIDDLE, self::SEPARATOR_Y_END],
        ];

        foreach ($lines as [$x1, $y1, $x2, $y2]) {
            $this->addContent($this->tcLibPdf->graph->getLine(
                $this->mm($x1 + $this->offsetX),
                $this->mm($y1 + $this->offsetY),
                $this->mm($x2 + $this->offsetX),
                $this->mm($y2 + $this->offsetY),
                $style
            ));
        }
    }

    private function addScissors(bool $positionAtBottom): void
    {
        $this->setSymbolFont(self::FONT_SIZE_SCISSORS);

        // horizontal scissors
        $this->addTextAt(self::FONT_SYMBOLS_CHAR_SCISSORS, self::SEPARATOR_X_START + 3, self::SEPARATOR_Y + 1.7);

        // vertical scissors
        if ($positionAtBottom) {
            $this->addTextAt(self::FONT_SYMBOLS_CHAR_SCISSORS, self::SEPARATOR_X_MIDDLE + 1.7, self::SEPARATOR_Y + 90, 90);
        } else {
            $this->addTextAt(self::FONT_SYMBOLS_CHAR_SCISSORS, self::SEPARATOR_X_MIDDLE - 1.7, self::SEPARATOR_Y + 4, -90);
        }
    }

    private function addSeparatorText(bool $displayDownArrows): void
    {
        $text = Translation::get('separate', $this->language);

        $this->cellHeightRatio = self::CELL_HEIGHT_RATIO_DEFAULT;
        $this->setFont(false, self::FONT_SIZE_FURTHER_INFORMATION);
        $this->y = self::SEPARATOR_Y - 5;
        $baseline = $this->getBaseline($this->getLineHeight());
        $textWidth = $this->getTextWidth($text);
        $this->addTextAt($text, self::PAGE_CENTER_X - $textWidth / 2, $baseline);

        if (!$displayDownArrows) {
            return;
        }

        $arrows = str_repeat(self::FONT_SYMBOLS_CHAR_DOWN_ARROW, 3);
        $arrowMargin = 1;

        $this->setSymbolFont(self::FONT_SIZE_DOWN_ARROW);
        $arrowsWidth = $this->getTextWidth($arrows);
        $this->addTextAt($arrows, self::PAGE_CENTER_X - $textWidth / 2 - $arrowMargin - $arrowsWidth, $baseline + 0.2);
        $this->addTextAt($arrows, self::PAGE_CENTER_X + $textWidth / 2 + $arrowMargin, $baseline + 0.2);
    }

    private function setContentElement(OutputElementInterface $element, bool $isReceiptPart, float $x, float $width): void
    {
        if ($element instanceof FurtherInformation) {
            $this->setFont(false, self::FONT_SIZE_FURTHER_INFORMATION);
            $this->printText($element->getText(), $x, $width);
        }

        if ($element instanceof Title) {
            $this->setFont(true, $isReceiptPart ? self::FONT_SIZE_TITLE_RECEIPT : self::FONT_SIZE_TITLE_PAYMENT_PART);
            $this->printLine(Translation::get(str_replace('text.', '', $element->getTitle()), $this->language) ?? '', $x);
        }

        if ($element instanceof Text) {
            $this->setFont(false, $isReceiptPart ? self::FONT_SIZE_RECEIPT : self::FONT_SIZE_PAYMENT_PART);
            $this->printText(str_replace('text.', '', $element->getText()), $x, $width);
            $this->y += $isReceiptPart ? self::LINE_SPACING_RECEIPT : self::LINE_SPACING_PAYMENT_PART;
        }

        if ($element instanceof Placeholder) {
            $this->setPlaceholderElement($element, $x);
        }
    }

    private function setPlaceholderElement(Placeholder $element, float $x): void
    {
        [$offsetX, $offsetY] = match ($element->getType()) {
            Placeholder::PLACEHOLDER_TYPE_AMOUNT['type'] => [-3, 1],
            Placeholder::PLACEHOLDER_TYPE_AMOUNT_RECEIPT['type'] => [10, -2],
            default => [0, 1],
        };

        $file = $element->getFile(Placeholder::FILE_TYPE_PNG);
        $image = @file_get_contents($file);
        if ($image === false) {
            throw new \RuntimeException(sprintf('Unable to read placeholder image "%s".', $file));
        }

        $this->addImage($image, $x + $offsetX, $this->y + $offsetY, (float) $element->getWidth(), (float) $element->getHeight());
    }

    /**
     * Prints text with line breaks, wrapping lines which exceed the given width.
     */
    private function printText(string $text, float $x, float $width): void
    {
        foreach (explode("\n", $text) as $paragraph) {
            foreach ($this->wrap($paragraph, $width) as $line) {
                $this->printLine($line, $x);
            }
        }
    }

    /**
     * Prints a single line, vertically centered within the given height, and moves the cursor below it.
     */
    private function printLine(
        string $text,
        float $x,
        ?float $height = null,
        string $align = self::ALIGN_LEFT,
        float $width = self::WIDTH_UNLIMITED
    ): void {
        $height ??= $this->getLineHeight();

        $x += match ($align) {
            self::ALIGN_RIGHT => $width - $this->getTextWidth($text),
            default => 0,
        };

        $this->addTextAt($text, $x, $this->getBaseline($height));
        $this->y += $height;
    }

    /**
     * @return list<string>
     */
    private function wrap(string $text, float $width): array
    {
        if ($width <= 0 || $this->getTextWidth($text) <= $width) {
            return [$text];
        }

        $lines = [];
        $line = '';
        foreach (explode(' ', $text) as $word) {
            $candidate = $line === '' ? $word : $line . ' ' . $word;
            if ($this->getTextWidth($candidate) <= $width) {
                $line = $candidate;
                continue;
            }

            if ($line !== '') {
                $lines[] = $line;
            }

            // Break words which are too long for a single line
            $line = '';
            foreach (mb_str_split($word) as $char) {
                if ($line !== '' && $this->getTextWidth($line . $char) > $width) {
                    $lines[] = $line;
                    $line = '';
                }
                $line .= $char;
            }
        }
        $lines[] = $line;

        return $lines;
    }

    private function setFont(bool $bold, float $size): void
    {
        $key = $bold ? $this->fontBold : $this->font;
        $style = $bold && $this->fontBold === $this->font ? 'B' : '';

        $this->insertFont($key, $style, $size);
    }

    private function setSymbolFont(float $size): void
    {
        $this->insertFont($this->fontSymbols, '', $size);
    }

    private function insertFont(string $key, string $style, float $size): void
    {
        $font = $this->tcLibPdf->font->insert($this->tcLibPdf->pon, $key, $style, $size);
        $this->addContent($font['out']);
        $this->fontSize = $size;
        $this->fontAscent = $font['ascent'];
        $this->fontDescent = $font['descent'];
    }

    private function getLineHeight(): float
    {
        return $this->fontSize * $this->cellHeightRatio / self::POINTS_PER_MM;
    }

    /**
     * Returns the baseline of text which is vertically centered in a line with the given height at the cursor.
     */
    private function getBaseline(float $lineHeight): float
    {
        return $this->y + $lineHeight / 2 + ($this->fontAscent + $this->fontDescent) / 2 / self::POINTS_PER_MM;
    }

    /**
     * Returns the width in mm of the given text in the current font.
     */
    private function getTextWidth(string $text): float
    {
        if ($text === '') {
            return 0;
        }

        $this->tcLibPdf->getTextLine($text);

        return $this->tcLibPdf->toPoints($this->tcLibPdf->getLastBBox()['w']) / self::POINTS_PER_MM;
    }

    /**
     * Adds text in the current font, starting at the given position on its baseline.
     *
     * @param float $angle Counter-clockwise rotation in degrees around the given position.
     */
    private function addTextAt(string $text, float $x, float $baseline, float $angle = 0): void
    {
        if ($text === '') {
            return;
        }

        $x = $this->mm($x + $this->offsetX);
        $baseline = $this->mm($baseline + $this->offsetY);
        $content = $this->tcLibPdf->getTextLine($text, $x, $baseline);

        if ($angle !== 0.0) {
            $content = $this->tcLibPdf->graph->getStartTransform()
                . $this->tcLibPdf->graph->getRotation($angle, $x, $baseline)
                . $content
                . $this->tcLibPdf->graph->getStopTransform();
        }

        $this->addContent($content);
    }

    /**
     * @param string $image Raw data of a png image
     */
    private function addImage(string $image, float $x, float $y, float $width, float $height): void
    {
        $imageId = $this->tcLibPdf->image->add('@' . $image);

        $this->addContent($this->tcLibPdf->image->getSetImage(
            $imageId,
            $this->mm($x + $this->offsetX),
            $this->mm($y + $this->offsetY),
            $this->mm($width),
            $this->mm($height),
            $this->tcLibPdf->page->getPage($this->pageId)['height']
        ));
    }

    private function addContent(string $content): void
    {
        $this->tcLibPdf->page->addContent($content, $this->pageId);
    }

    /**
     * Converts millimeters to the unit of the pdf.
     */
    private function mm(float $mm): float
    {
        return $this->tcLibPdf->toUnit($mm * self::POINTS_PER_MM);
    }
}
