<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\PaymentPart\Output\TcLibPdfOutput;

use Com\Tecnick\Pdf\Tcpdf;
use Sprain\SwissQrBill\PaymentPart\Output\AbstractOutput;
use Sprain\SwissQrBill\PaymentPart\Output\LineStyle;
use Sprain\SwissQrBill\PaymentPart\Output\Element\FurtherInformation;
use Sprain\SwissQrBill\PaymentPart\Output\Element\OutputElementInterface;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Placeholder;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Text;
use Sprain\SwissQrBill\PaymentPart\Output\Element\Title;
use Sprain\SwissQrBill\PaymentPart\Translation\Translation;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\QrCode\QrCode;

/**
 * Payment part output for tecnickcom/tc-lib-pdf (the modern, modular
 * successor of TCPDF).
 *
 * Differences to TcPdfOutput (classic TCPDF):
 *  - tc-lib-pdf has no internal text cursor (SetX/SetY/Cell/Ln). This class
 *    keeps its own Y cursor and positions content absolutely via
 *    addTextCellXY(); consumed heights are read back from getLastCellBBox().
 *  - The QR code and the corner mark placeholders are embedded as PNG
 *    ('@' prefix = raw image data), so no SVG pipeline is required.
 *  - Scissors glyphs (✂) would require an embedded unicode font and are
 *    omitted; separation line and hint text are drawn instead.
 *
 * Fonts: tc-lib-pdf resolves font keys via K_PATH_FONTS. The defaults use
 * the 'helvetica' core font. If your document embeds custom fonts (e.g. a
 * corporate font converted with tc-lib-pdf-font), pass their font keys via
 * $fontRegular / $fontBold. The Swiss QR bill style guide permits Arial,
 * Frutiger, Helvetica and Liberation Sans.
 *
 * IMPORTANT - page margins: tc-lib-pdf clips text cells to the page's
 * content region. The payment part occupies the bottom 105 mm of the page
 * (y = 188-297 mm on A4), so the target page must be created with margins
 * that do not clip this area (e.g. margin 'CB' = 0). Otherwise parts of
 * the bill (amount, acceptance point) are silently dropped.
 */
final class TcLibPdfOutput extends AbstractOutput
{
    private const ALIGN_LEFT = 'L';
    private const ALIGN_RIGHT = 'R';
    private const ALIGN_CENTER = 'C';
    private const FONT = 'helvetica';

    /** pt → mm */
    private const PT_TO_MM = 0.352777778;

    // Line height factors (match the cellHeightRatio values in TcPdfOutput)
    private const LEFT_CELL_HEIGHT_RATIO_COMMON = 1.2;
    private const RIGHT_CELL_HEIGHT_RATIO_COMMON = 1.1;

    // Positioning (mm, same as TcPdfOutput / Swiss QR bill spec)
    private const CURRENCY_AMOUNT_Y = 259;
    private const LEFT_PART_X = 4;
    private const RIGHT_PART_X = 66;
    private const RIGHT_PART_X_INFO = 117;
    private const TITLE_Y = 195;

    // Column widths (mm)
    private const WIDTH_COLUMN_RECEIPT = 54;
    private const WIDTH_COLUMN_PAYMENT_INFO = 87;

    // Font sizes (pt)
    private const FONT_SIZE_MAIN_TITLE = 11;
    private const FONT_SIZE_TITLE_RECEIPT = 6;
    private const FONT_SIZE_RECEIPT = 8;
    private const FONT_SIZE_TITLE_PAYMENT_PART = 8;
    private const FONT_SIZE_PAYMENT_PART = 10;
    private const FONT_SIZE_FURTHER_INFORMATION = 7;

    // Spacing after a text element (mm)
    private const LINE_SPACING_RECEIPT = 3.5;
    private const LINE_SPACING_PAYMENT_PART = 4.8;

    /** Y cursor (mm, absolute page coordinate) */
    private float $currentY = 0;

    /** Page id the payment part is drawn on */
    private int $pid = -1;

    /**
     * @param Tcpdf  $tcLibPdf    Target pdf (the current page is drawn on)
     * @param string $fontRegular Font key, must be resolvable via K_PATH_FONTS
     * @param string $fontBold    Font key of the bold cut. If it equals
     *                            $fontRegular, style 'B' is requested instead.
     */
    public function __construct(
        QrBill $qrBill,
        string $language,
        private readonly Tcpdf $tcLibPdf,
        private readonly float $offsetX = 0,
        private readonly float $offsetY = 0,
        private readonly string $fontRegular = self::FONT,
        private readonly string $fontBold = self::FONT
    ) {
        parent::__construct($qrBill, $language);
        $this->setQrCodeImageFormat(QrCode::FILE_FORMAT_PNG);
    }

    public function getPaymentPart(): ?string
    {
        $this->pid = (int) $this->tcLibPdf->page->getPageId();

        $retainAutoPageBreak = $this->tcLibPdf->page->isAutoPageBreakEnabled($this->pid);
        $this->tcLibPdf->page->enableAutoPageBreak(false, $this->pid);

        $this->addSeparatorContentIfNotPrintable();

        $this->addInformationContentReceipt();
        $this->addCurrencyContentReceipt();
        $this->addAmountContentReceipt();

        $this->addSwissQrCodeImage();
        $this->addInformationContent();
        $this->addCurrencyContent();
        $this->addAmountContent();
        $this->addFurtherInformationContent();

        $this->tcLibPdf->page->enableAutoPageBreak($retainAutoPageBreak, $this->pid);

        return null;
    }

    private function addSwissQrCodeImage(): void
    {
        $qrCode = $this->getQrCode();

        $yPosQrCode = 209.5 + $this->offsetY;
        $xPosQrCode = self::RIGHT_PART_X + 1 + $this->offsetX;

        $this->placeImage($qrCode->getAsString($this->getQrCodeImageFormat()), $xPosQrCode, $yPosQrCode, 46, 46);
    }

    private function addInformationContentReceipt(): void
    {
        $x = self::LEFT_PART_X;

        // Title
        $this->setFont('B', self::FONT_SIZE_MAIN_TITLE);
        $this->currentY = self::TITLE_Y;
        $this->printLine(Translation::get('receipt', $this->language), $x, self::WIDTH_COLUMN_RECEIPT, 7);

        // Elements
        $this->currentY = 204;
        foreach ($this->getInformationElementsOfReceipt() as $informationElement) {
            $this->setContentElement($informationElement, true, $x, self::WIDTH_COLUMN_RECEIPT);
        }

        // Acceptance section
        $this->setFont('B', 6);
        $this->currentY = 273;
        $this->printLine(
            Translation::get('acceptancePoint', $this->language),
            $x,
            self::WIDTH_COLUMN_RECEIPT,
            0,
            self::ALIGN_RIGHT
        );
    }

    private function addInformationContent(): void
    {
        // Title
        $this->setFont('B', self::FONT_SIZE_MAIN_TITLE);
        $this->currentY = self::TITLE_Y;
        $this->printLine(Translation::get('paymentPart', $this->language), self::RIGHT_PART_X, 48, 7);

        // Elements
        $this->currentY = 197;
        foreach ($this->getInformationElements() as $informationElement) {
            $this->setContentElement($informationElement, false, self::RIGHT_PART_X_INFO, self::WIDTH_COLUMN_PAYMENT_INFO);
        }
    }

    private function addCurrencyContentReceipt(): void
    {
        $this->currentY = self::CURRENCY_AMOUNT_Y;
        foreach ($this->getCurrencyElements() as $currencyElement) {
            $this->setContentElement($currencyElement, true, self::LEFT_PART_X, 10);
        }
    }

    private function addAmountContentReceipt(): void
    {
        $this->currentY = self::CURRENCY_AMOUNT_Y;
        foreach ($this->getAmountElementsReceipt() as $amountElement) {
            $this->setContentElement($amountElement, true, 16, 30);
        }
    }

    private function addCurrencyContent(): void
    {
        $this->currentY = self::CURRENCY_AMOUNT_Y;
        foreach ($this->getCurrencyElements() as $currencyElement) {
            $this->setContentElement($currencyElement, false, self::RIGHT_PART_X, 10);
        }
    }

    private function addAmountContent(): void
    {
        $this->currentY = self::CURRENCY_AMOUNT_Y;
        foreach ($this->getAmountElements() as $amountElement) {
            $this->setContentElement($amountElement, false, 80, 35);
        }
    }

    private function addFurtherInformationContent(): void
    {
        $this->currentY = 286;
        foreach ($this->getFurtherInformationElements() as $furtherInformationElement) {
            $this->setContentElement($furtherInformationElement, false, self::RIGHT_PART_X, 138);
        }
    }

    private function addSeparatorContentIfNotPrintable(): void
    {
        $layout = $this->getDisplayOptions();
        if ($layout->isPrintable()) {
            return;
        }

        $xstart = 2;
        $xmiddle = 62;
        $y = 193;
        $yend = 296;

        if ($layout->getLineStyle() !== LineStyle::NONE) {
            $style = [
                'lineWidth' => 0.1,
                'lineColor' => 'black',
                'lineCap' => 'butt',
            ];
            if ($layout->getLineStyle() === LineStyle::DASHED) {
                $style['dashArray'] = [2];
            }
            $this->tcLibPdf->page->addContent(
                $this->tcLibPdf->graph->getLine(
                    $xstart + $this->offsetX,
                    $y + $this->offsetY,
                    208 + $this->offsetX,
                    $y + $this->offsetY,
                    $style
                ),
                $this->pid
            );
            $this->tcLibPdf->page->addContent(
                $this->tcLibPdf->graph->getLine(
                    $xmiddle + $this->offsetX,
                    $y + $this->offsetY,
                    $xmiddle + $this->offsetX,
                    $yend + $this->offsetY,
                    $style
                ),
                $this->pid
            );
        }

        // Note: scissors glyphs (✂) require an embedded unicode font and are
        // intentionally not drawn (the Helvetica core font has no such glyph).
        // Separation line plus hint text cover the requirement.

        if ($layout->isDisplayText()) {
            $this->setFont('', self::FONT_SIZE_FURTHER_INFORMATION);
            $this->currentY = $y - 5;
            $this->printLine(Translation::get('separate', $this->language), 5, 200, 0, self::ALIGN_CENTER);
        }
    }

    private function setContentElement(OutputElementInterface $element, bool $isReceiptPart, float $x, float $width): void
    {
        if ($element instanceof FurtherInformation) {
            $this->setFont('', self::FONT_SIZE_FURTHER_INFORMATION);
            $this->printMultiLine($element->getText(), $x, $width, self::FONT_SIZE_FURTHER_INFORMATION, 1.5);
        }

        if ($element instanceof Title) {
            $this->setFont('B', $isReceiptPart ? self::FONT_SIZE_TITLE_RECEIPT : self::FONT_SIZE_TITLE_PAYMENT_PART);
            $this->printMultiLine(
                Translation::get(str_replace('text.', '', $element->getTitle()), $this->language),
                $x,
                $width,
                $isReceiptPart ? self::FONT_SIZE_TITLE_RECEIPT : self::FONT_SIZE_TITLE_PAYMENT_PART,
                $isReceiptPart ? self::LEFT_CELL_HEIGHT_RATIO_COMMON : self::RIGHT_CELL_HEIGHT_RATIO_COMMON
            );
        }

        if ($element instanceof Text) {
            $this->setFont('', $isReceiptPart ? self::FONT_SIZE_RECEIPT : self::FONT_SIZE_PAYMENT_PART);
            $this->printMultiLine(
                str_replace('text.', '', $element->getText()),
                $x,
                $width,
                $isReceiptPart ? self::FONT_SIZE_RECEIPT : self::FONT_SIZE_PAYMENT_PART,
                $isReceiptPart ? self::LEFT_CELL_HEIGHT_RATIO_COMMON : self::RIGHT_CELL_HEIGHT_RATIO_COMMON
            );
            $this->currentY += $isReceiptPart ? self::LINE_SPACING_RECEIPT : self::LINE_SPACING_PAYMENT_PART;
        }

        if ($element instanceof Placeholder) {
            $this->setPlaceholderElement($element);
        }
    }

    private function setPlaceholderElement(Placeholder $element): void
    {
        $type = $element->getType();

        switch ($type) {
            case Placeholder::PLACEHOLDER_TYPE_AMOUNT['type']:
                $y = $this->currentY + 1;
                $x = 78;
                break;
            case Placeholder::PLACEHOLDER_TYPE_AMOUNT_RECEIPT['type']:
                $y = $this->currentY - 2;
                $x = 27;
                break;
            case Placeholder::PLACEHOLDER_TYPE_PAYABLE_BY['type']:
                $y = $this->currentY + 1;
                $x = self::RIGHT_PART_X_INFO + 1;
                break;
            case Placeholder::PLACEHOLDER_TYPE_PAYABLE_BY_RECEIPT['type']:
            default:
                $y = $this->currentY + 1;
                $x = self::LEFT_PART_X + 1;
        }

        $png = @file_get_contents($element->getFile(Placeholder::FILE_TYPE_PNG));
        if ($png !== false) {
            $this->placeImage(
                $png,
                $x + $this->offsetX,
                $y + $this->offsetY,
                (float) $element->getWidth(),
                (float) $element->getHeight()
            );
        }
    }

    /**
     * Activates font/style/size for the following page content.
     * If a dedicated bold font key is configured, it is used without the
     * style flag (the font file itself is the bold cut).
     */
    private function setFont(string $style, float $sizePt): void
    {
        $key = $this->fontRegular;
        if ($style === 'B') {
            $key = $this->fontBold;
            if ($this->fontBold !== $this->fontRegular) {
                $style = '';
            }
        }
        $font = $this->tcLibPdf->font->insert($this->tcLibPdf->pon, $key, $style, (int) $sizePt);
        $this->tcLibPdf->page->addContent($font['out'], $this->pid);
        $this->tcLibPdf->page->addContent($this->tcLibPdf->graph->getStyleCmd(['fillColor' => 'black']), $this->pid);
    }

    /**
     * Prints one line at an absolute position; the Y cursor advances by
     * $advance mm (0 = by the actually consumed height).
     */
    private function printLine(string $text, float $x, float $width, float $advance = 0, string $halign = self::ALIGN_LEFT): void
    {
        if ($text === '') {
            $this->currentY += $advance;
            return;
        }

        $this->tcLibPdf->addTextCellXY(
            txt: $text,
            pid: $this->pid,
            posx: $x + $this->offsetX,
            posy: $this->currentY + $this->offsetY,
            width: $width,
            height: 0,
            valign: 'T',
            halign: $halign,
            drawcell: false,
        );

        if ($advance > 0) {
            $this->currentY += $advance;
            return;
        }

        $bbox = $this->tcLibPdf->getLastCellBBox();
        $this->currentY += (float) ($bbox['h'] ?? 0);
    }

    /**
     * Prints multi-line text ("\n"-separated); each line advances the cursor
     * by the line height (font size × ratio) or the actually consumed height,
     * whichever is bigger (long lines may wrap within $width).
     */
    private function printMultiLine(string $text, float $x, float $width, float $fontSizePt, float $ratio): void
    {
        $lineHeight = $fontSizePt * self::PT_TO_MM * $ratio;

        foreach (explode("\n", $text) as $line) {
            if ($line === '') {
                $this->currentY += $lineHeight;
                continue;
            }

            $this->tcLibPdf->addTextCellXY(
                txt: $line,
                pid: $this->pid,
                posx: $x + $this->offsetX,
                posy: $this->currentY + $this->offsetY,
                width: $width,
                height: 0,
                valign: 'T',
                halign: self::ALIGN_LEFT,
                drawcell: false,
            );

            $bbox = $this->tcLibPdf->getLastCellBBox();
            $this->currentY += max($lineHeight, (float) ($bbox['h'] ?? 0));
        }
    }

    /**
     * Places raw PNG data (QR code, corner marks) at an absolute position.
     */
    private function placeImage(string $pngData, float $x, float $y, float $width, float $height): void
    {
        $iid = $this->tcLibPdf->image->add('@' . $pngData);
        $page = $this->tcLibPdf->page->getPage($this->pid);
        $this->tcLibPdf->page->addContent(
            $this->tcLibPdf->image->getSetImage($iid, $x, $y, $width, $height, (float) $page['height']),
            $this->pid
        );
    }
}
