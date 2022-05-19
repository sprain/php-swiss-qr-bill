<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\Table;

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
		$this->titleSection = $this->table->addRow(PhpWordHelper::mmToTwip(Style::TITLE_SECTION_HEIGHT))->addCell();
		$this->informationSection = $this->table->addRow(PhpWordHelper::mmToTwip(Style::RECEIPT_INFORMATION_SECTION_HEIGHT))->addCell();
		$this->amountSection = new AmountSection(
				$this->table->addRow(PhpWordHelper::mmToTwip(Style::RECEIPT_AMOUNT_SECTION_HEIGHT))->addCell(),
				Style::RECEIPT_CURRENCY_WIDTH,
				Style::RECEIPT_AMOUNT_WIDTH,
				Style::RECEIPT_AMOUNT_SECTION_HEIGHT);
		$this->acceptancePointSection = $this->table->addRow(PhpWordHelper::mmToTwip(Style::RECEIPT_ACCEPTANCE_SECTION_HEIGHT))->addCell();
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
