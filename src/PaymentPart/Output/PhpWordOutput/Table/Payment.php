<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\Table;

use PhpOffice\PhpWord\Element\Cell;
use Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\PhpWordHelper;
use Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\Table\Receipt\AmountSection;
use PhpOffice\PhpWord\Element\Table;

class Payment
{
    private Table $table;
    private Cell $titleSection;
    private Cell $qrCodeSection;
    private AmountSection $amountSection;
    private Cell $informationSection;
    private Cell $furtherInformationSection;

    public function __construct(Cell $cell)
    {
        $this->table = $cell->addTable([
                'layout' => \PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED,
                'width' => PhpWordHelper::percentToPct(100),
                'unit' => 'pct',
        ]);
        $this->table->getStyle()->setLayout(\PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED);

        $row = $this->table->addRow(PhpWordHelper::mmToTwip(Style::PAYMENT_PART_INFORMATION_SECTION_HEIGHT));
        $paymentPartLeftCell = $row->addCell(PhpWordHelper::mmToTwip(Style::PAYMENT_PART_INNER_WIDTH - Style::PAYMENT_PART_INFORMATION_SECTION_WIDTH));
        $this->informationSection = $row->addCell(PhpWordHelper::mmToTwip(Style::PAYMENT_PART_INFORMATION_SECTION_WIDTH));

        $table = $paymentPartLeftCell->addTable([
                'layout' => \PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED,
                'width' => PhpWordHelper::percentToPct(100),
                'unit' => 'pct',
        ]);
        $row = $table->addRow(PhpWordHelper::mmToTwip(Style::TITLE_SECTION_HEIGHT));
        $this->titleSection = $row->addCell();
        $this->addSpacerBeforeQrCode($table);
        $row = $table->addRow(PhpWordHelper::mmToTwip(Style::QR_CODE_SIZE_WITH_BOTTOM_SPACE));
        $this->qrCodeSection = $row->addCell();
        $row = $table->addRow(PhpWordHelper::mmToTwip(Style::PAYMENT_PART_AMOUNT_SECTION_HEIGHT));
        $this->amountSection = new AmountSection(
            $row->addCell(),
            Style::PAYMENT_PART_CURRENCY_WIDTH,
            Style::PAYMENT_PART_AMOUNT_WIDTH,
            Style::PAYMENT_PART_AMOUNT_SECTION_HEIGHT
        );

        $row = $this->table->addRow(PhpWordHelper::mmToTwip(Style::PAYMENT_PART_FURTHER_INFORMATION_SECTION_HEIGHT));
        $this->furtherInformationSection = $row->addCell(null, ['gridSpan' => 2]);
    }

    public function getTitleSection() : Cell
    {
        return $this->titleSection;
    }

    public function getQrCodeSection() : Cell
    {
        return $this->qrCodeSection;
    }

    public function getAmountSection() : AmountSection
    {
        return $this->amountSection;
    }

    public function getInformationSection() : Cell
    {
        return $this->informationSection;
    }

    public function getFurtherInformationSection() : Cell
    {
        return $this->furtherInformationSection;
    }

    private function addSpacerBeforeQrCode(Table $table) : void
    {
        $fontSizeMustBeSmallThanRowHeight = 7;
        $table->addRow(PhpWordHelper::mmToTwip(5))->addCell()->addText('', ['size' => $fontSizeMustBeSmallThanRowHeight], ['spaceAfter' => 0]);
    }
}
