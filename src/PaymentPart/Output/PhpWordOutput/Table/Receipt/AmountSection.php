<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\Table\Receipt;

use PhpOffice\PhpWord\Element\Cell;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Table;
use Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\PhpWordHelper;

class AmountSection {

	private Cell $currencyCell;
	private Cell $amountCell;

	public function __construct(Cell $cell, float $currencyWidth, float $amountWidth, float $height) {
		$table = $cell->addTable([
				'layout' => Table::LAYOUT_FIXED,
				'width' => PhpWordHelper::percentToPct(100),
				'unit' => 'pct',
		]);
		$row = $table->addRow(PhpWordHelper::mmToTwip($height));
		$this->currencyCell = $row->addCell(PhpWordHelper::mmToTwip($currencyWidth));
		$this->amountCell = $row->addCell(PhpWordHelper::mmToTwip($amountWidth));
	}

	public function getCurrencyCell() : Cell {
		return $this->currencyCell;
	}

	public function getAmountCell() : Cell {
		return $this->amountCell;
	}

}
