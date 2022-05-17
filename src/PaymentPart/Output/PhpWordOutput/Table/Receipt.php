<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\Table;

use PhpOffice\PhpWord\Shared\Converter;
use Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\Table\Receipt\AmountSection;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Cell;

class Receipt {

	private Table $table;
	private Cell $titleSection;
	private Cell $informationSection;
	private AmountSection $amountSection;
	private Cell $acceptancePointSection;

	public function __construct(Cell $cell) {
		$this->table = $cell->addTable([
				'layout' => \PhpOffice\PhpWord\Style\Table::LAYOUT_FIXED,
				'width' => 100 * 50,
				'unit' => 'pct',
		]);
		$this->titleSection = $this->table->addRow(Converter::cmToTwip(0.7))->addCell();
		$this->informationSection = $this->table->addRow(Converter::cmToTwip(5.6))->addCell();
		$this->amountSection = new AmountSection($this->table->addRow(Converter::cmToTwip(1.4))->addCell(), 1.22, 5.2 - 1.22, 1.4);
		$this->acceptancePointSection = $this->table->addRow(Converter::cmToTwip(1.8))->addCell();
	}

	public function getTitleSection() : Cell {
		return $this->titleSection;
	}

	public function getInformationSection() : Cell {
		return $this->informationSection;
	}

	public function getAmountSection() : AmountSection {
		return $this->amountSection;
	}

	public function getAcceptancePointSection() : Cell {
		return $this->acceptancePointSection;
	}


}
