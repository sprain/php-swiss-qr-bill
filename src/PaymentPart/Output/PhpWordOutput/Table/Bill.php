<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\Table;

use PhpOffice\PhpWord\Element\Cell;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Border;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\Style\Table;
use PhpOffice\PhpWord\Style\TablePosition;

class Bill {
	private \PhpOffice\PhpWord\Element\Table $table;
	private ?Cell $separate = null;
	private Receipt $receipt;
	private Payment $payment;

	public function __construct(Section $section, bool $isPrintable) {
		$this->table = $section->addTable([
				'layout' => Table::LAYOUT_FIXED,
				'width' => Converter::cmToTwip(21),
				'height' => Converter::cmToTwip(10.5),
				'position' => [
						'horzAnchor' => TablePosition::HANCHOR_PAGE,
						'vertAnchor' => TablePosition::VANCHOR_PAGE,
						'tblpXSpec' => TablePosition::XALIGN_CENTER,
						'tblpYSpec' => TablePosition::YALIGN_BOTTOM,
						'tblpX' => Converter::cmToTwip(3.5),
						'leftFromText' => 0,
						'rightFromText' => 0,
						'topFromText' => 0,
						'bottomFromText' => 0,
				],
		]);
		$verticalLine = [];
		if (!$isPrintable) {
			$this->separate = $this->table->addRow(Converter::cmToTwip(0.5))->addCell(Converter::cmToTwip(21), [
					'borderBottomColor' => '000000',
					'borderBottomSize' => 1,
					'borderBottomStyle' => Border::SINGLE,
					'gridSpan' => 2,
					'valign' => JcTable::CENTER,
			]);
			$verticalLine = [
					'borderRightColor' => '000000',
					'borderRightSize' => 1,
					'borderRightStyle' => Border::SINGLE,
			];
		}
		$row = $this->table->addRow(Converter::cmToTwip(9.5));
		$cell = $row->addCell(Converter::cmToTwip(6.2), $verticalLine);
		$cell = $cell->addTable([
				'layout' => Table::LAYOUT_FIXED,
				'width' => 100 * 50,
				'unit' => 'pct',
				'cellMargin' => Converter::cmToTwip(0.5),
		])->addRow()->addCell();
		$this->receipt = new Receipt($cell);
		$cell = $row->addCell(Converter::cmToTwip(14.8));
		$cell = $cell->addTable([
				'layout' => Table::LAYOUT_FIXED,
				'width' => 100 * 50,
				'unit' => 'pct',
				'cellMargin' => Converter::cmToTwip(0.5),
		])->addRow()->addCell();
		$this->payment = new Payment($cell);
	}

	public function getSeparate() : ?Cell {
		return $this->separate;
	}

	public function getReceipt() : Receipt {
		return $this->receipt;
	}

	public function getPayment() : Payment {
		return $this->payment;
	}

}
