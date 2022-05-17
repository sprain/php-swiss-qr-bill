<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\Table\Receipt;

use PhpOffice\PhpWord\Element\Cell;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Table;

class AmountSection {

	private Cell $currencyCell;
	private Cell $amountCell;

	public function __construct(Cell $cell, float $currencyWidth, float $amountWidth, float $height) {
		$table = $cell->addTable([
				'layout' => Table::LAYOUT_FIXED,
				'width' => 100 * 50,
				'unit' => 'pct',
		]);
		$row = $table->addRow(Converter::cmToTwip($height));
		$this->currencyCell = $row->addCell(Converter::cmToTwip($currencyWidth));
		$this->amountCell = $row->addCell(Converter::cmToTwip($amountWidth));
	}

	public function getCurrencyCell() : Cell {
		return $this->currencyCell;
	}

	public function getAmountCell() : Cell {
		return $this->amountCell;
	}

}
