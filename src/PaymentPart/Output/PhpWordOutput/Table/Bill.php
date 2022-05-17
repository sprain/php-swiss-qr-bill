<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\PhpWordOutput\Table;

use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Table;
use PhpOffice\PhpWord\Style\TablePosition;

class Bill {
	private \PhpOffice\PhpWord\Element\Table $table;
	private Receipt $receipt;
	private Payment $payment;

	public function __construct(Section $section) {
		$this->table = $section->addTable([
				'layout' => Table::LAYOUT_FIXED,
				'width' => Converter::cmToTwip(21),
				'height' => Converter::cmToTwip(10.5),
				'cellMargin' => Converter::cmToTwip(0.5),
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
		$row = $this->table->addRow(Converter::cmToTwip(9.5));
		$cell = $row->addCell(Converter::cmToTwip(6.2));
		$this->receipt = new Receipt($cell);
		$cell = $row->addCell(Converter::cmToTwip(14.8));
		$this->payment = new Payment($cell);
	}

	public function getReceipt() : Receipt {
		return $this->receipt;
	}

	public function getPayment() : Payment {
		return $this->payment;
	}

}
