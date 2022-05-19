<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\Table;

use PhpOffice\PhpWord\Element\Cell;
use Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\PhpWordHelper;
use Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\Table\Receipt\AmountSection;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Shared\Converter;

class Payment {

	private Table $table;
	private Cell $titleSection;
	private Cell $qrCodeSection;
	private AmountSection $amountSection;
	private Cell $informationSection;
	private Cell $furtherInformationSection;

	public function __construct(Cell $cell) {
		$this->table = $cell->addTable([
				'layout' => \PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED,
				'width' => PhpWordHelper::percentToPct(100),
				'unit' => 'pct',
		]);
		$this->table->getStyle()->setLayout(\PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED);

		$row = $this->table->addRow(PhpWordHelper::mmToTwip(85));
		$paymentPartLeftCell = $row->addCell(PhpWordHelper::mmToTwip(56));
		$this->informationSection = $row->addCell(PhpWordHelper::mmToTwip(92));

		$table = $paymentPartLeftCell->addTable([
				'layout' => \PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED,
				'width' => PhpWordHelper::percentToPct(100),
				'unit' => 'pct',
		]);
		$row = $table->addRow(PhpWordHelper::mmToTwip(07));
		$this->titleSection = $row->addCell();
		$table->addRow(PhpWordHelper::mmToTwip(05))->addCell()->addText('', ['size' => 8], ['spaceAfter' => 0]);
		$row = $table->addRow(PhpWordHelper::mmToTwip(51));
		$this->qrCodeSection = $row->addCell();
		$row = $table->addRow(PhpWordHelper::mmToTwip(22));
		$this->amountSection = new AmountSection($row->addCell(), 14.4, 51 - 14.4, 22);

		$row = $this->table->addRow(PhpWordHelper::mmToTwip(10));
		$this->furtherInformationSection = $row->addCell(PhpWordHelper::mmToTwip(56), [
				'gridSpan' => 2,
		]);
	}

	public function getTitleSection() : Cell {
		return $this->titleSection;
	}

	public function getQrCodeSection() : Cell {
		return $this->qrCodeSection;
	}

	public function getAmountSection() : AmountSection {
		return $this->amountSection;
	}

	public function getInformationSection() : Cell {
		return $this->informationSection;
	}

	public function getFurtherInformationSection() : Cell {
		return $this->furtherInformationSection;
	}


}
