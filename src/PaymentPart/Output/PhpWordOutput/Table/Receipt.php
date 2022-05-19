<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\Table;

use PhpOffice\PhpWord\Shared\Converter;
use Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\PhpWordHelper;
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
				'width' => PhpWordHelper::percentToPct(100),
				'unit' => 'pct',
		]);
		$this->titleSection = $this->table->addRow(PhpWordHelper::mmToTwip(07))->addCell();
		$this->informationSection = $this->table->addRow(PhpWordHelper::mmToTwip(56))->addCell();
		$this->amountSection = new AmountSection($this->table->addRow(PhpWordHelper::mmToTwip(14))->addCell(), 12.2, 52 - 12.2, 14);
		$this->acceptancePointSection = $this->table->addRow(PhpWordHelper::mmToTwip(18))->addCell();
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
