<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\Table;

use PhpOffice\PhpWord\Element\Cell;
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
				'width' => 100 * 50,
				'unit' => 'pct',
		]);
		$this->table->getStyle()->setLayout(\PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED);

		$row = $this->table->addRow(Converter::cmToTwip(8.5));
		$paymentPartLeftCell = $row->addCell(Converter::cmToTwip(5.6));
		$this->informationSection = $row->addCell(Converter::cmToTwip(9.2));

		$table = $paymentPartLeftCell->addTable([
				'layout' => \PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED,
				'width' => 100 * 50,
				'unit' => 'pct',
		]);
		$row = $table->addRow(Converter::cmToTwip(0.7));
		$this->titleSection = $row->addCell();
		$table->addRow(Converter::cmToTwip(0.5))->addCell()->addText('', ['size' => 8], ['spaceAfter' => 0]);
		$row = $table->addRow(Converter::cmToTwip(5.1));
		$this->qrCodeSection = $row->addCell();
		$row = $table->addRow(Converter::cmToTwip(2.2));
		$this->amountSection = new AmountSection($row->addCell(), 1.44, 5.1 - 1.44, 2.2);

		$row = $this->table->addRow(Converter::cmToTwip(1.0));
		$this->furtherInformationSection = $row->addCell(Converter::cmToTwip(5.6), [
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
