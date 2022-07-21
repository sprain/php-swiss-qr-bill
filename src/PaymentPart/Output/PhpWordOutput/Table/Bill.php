<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\Table;

use PhpOffice\PhpWord\Element\Cell;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\Style\Table;
use PhpOffice\PhpWord\Style\TablePosition;
use Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\PhpWordHelper;

class Bill
{
    private \PhpOffice\PhpWord\Element\Table $table;
    private ?Cell $separate = null;
    private Receipt $receipt;
    private Payment $payment;

    public function __construct(Section $section, bool $isPrintable)
    {
        $this->table = $section->addTable([
                'layout' => Table::LAYOUT_FIXED,
                'width' => PhpWordHelper::mmToTwip(Style::DIN_A4_WIDTH),
                'height' => PhpWordHelper::mmToTwip(Style::DIN_A6_WIDTH),
                'position' => [
                        'horzAnchor' => TablePosition::HANCHOR_PAGE,
                        'vertAnchor' => TablePosition::VANCHOR_PAGE,
                        'tblpXSpec' => TablePosition::XALIGN_CENTER,
                        'tblpYSpec' => TablePosition::YALIGN_BOTTOM,
                        'leftFromText' => 0,
                        'rightFromText' => 0,
                        'topFromText' => 0,
                        'bottomFromText' => 0,
                ],
        ]);
        $verticalLine = [];
        if (!$isPrintable) {
            $width = PhpWordHelper::mmToTwip(Style::DIN_A4_WIDTH);
            $height = PhpWordHelper::mmToTwip(5);
            $separatorCellStyle = [
                    'borderBottomColor' => Style::NON_PRINTABLE_BORDER_COLOR,
                    'borderBottomSize' => Style::NON_PRINTABLE_BORDER_SIZE,
                    'borderBottomStyle' => Style::NON_PRINTABLE_BORDER_TYPE,
                    'gridSpan' => 2,
                    'valign' => JcTable::CENTER,
            ];
            $this->separate = $this->table->addRow($height)->addCell($width, $separatorCellStyle);
            $verticalLine = [
                    'borderRightColor' => Style::NON_PRINTABLE_BORDER_COLOR,
                    'borderRightSize' => Style::NON_PRINTABLE_BORDER_SIZE,
                    'borderRightStyle' => Style::NON_PRINTABLE_BORDER_TYPE,
            ];
        }
        $row = $this->table->addRow(PhpWordHelper::mmToTwip(Style::INNER_HEIGHT));
        $cell = $row->addCell(PhpWordHelper::mmToTwip(Style::RECEIPT_WIDTH), $verticalLine);
        $cell = $cell->addTable([
                'layout' => Table::LAYOUT_FIXED,
                'width' => PhpWordHelper::percentToPct(100),
                'unit' => 'pct',
                'cellMargin' => PhpWordHelper::mmToTwip(5),
        ])->addRow()->addCell();
        $this->receipt = new Receipt($cell);

        $cell = $row->addCell(PhpWordHelper::mmToTwip(Style::PAYMENT_PART_WIDTH));
        $cell = $cell->addTable([
                'layout' => Table::LAYOUT_FIXED,
                'width' => PhpWordHelper::percentToPct(100),
                'unit' => 'pct',
                'cellMargin' => PhpWordHelper::mmToTwip(5),
        ])->addRow()->addCell();
        $this->payment = new Payment($cell);
    }

    public function getSeparate() : ?Cell
    {
        return $this->separate;
    }

    public function getReceipt() : Receipt
    {
        return $this->receipt;
    }

    public function getPayment() : Payment
    {
        return $this->payment;
    }
}
