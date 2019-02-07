<?php

namespace Sprain\SwissQrBill\PaymentPart\HtmlOutput\Template;

class PaymentPartTemplate
{
    public const TEMPLATE = <<<EOT
<style>
#qr-bill-payment-part {
	box-sizing: border-box;
	width: 148mm;
	height: 105mm;
	padding: 6mm 8mm;
	border: 0.2mm solid black;
	font-family: Arial, Frutiger, Helvetica;
}

#qr-bill-payment-part h1 {
	font-size: 11pt;
	font-weight: bold;
	margin: 0;
	padding: 0;
}

#qr-bill-payment-part h2 {
	font-size: 7pt;
	font-weight: bold;
	margin: 0;
	margin-bottom: 1mm;
	margin-top: 2.5mm;
	padding: 0;
}

#qr-bill-payment-part h2:first-child {
	margin-top: 0;
}

#qr-bill-payment-part p {
	font-size: 9pt;
	line-height: 10pt;
	font-weight: normal;
	margin: 0;
	padding: 0;
}

#qr-bill-title {
	width: 100%;
	height: 8mm;
}

#qr-bill-left-column {
	float: left;
	width: 46mm;
	vertical-align: top;
}

#qr-bill-right-column {
	box-sizing: border-box;
	width: 86mm;
	padding-left: 8mm;
	padding-right: 8mm;
	vertical-align: top;
}

#qr-bill-scheme {
	box-sizing: border-box;
	width: 46mm;
}

#qr-bill-swiss-qr {
	margin-top: 4mm;
	margin-bottom: 4mm;
	background-color: #ddd;
}

#qr-bill-swiss-qr-image {
	width: 46mm;
	height: 46mm;
}

#qr-bill-amount {
	box-sizing: border-box;
	width: 46mm;
}

#qr-bill-currency {
	float: left;
	margin-right: 2mm;
}

#qr-bill-content {
	border-collapse: collapse;
}
</style>

<div id="qr-bill-payment-part">

	<div id="qr-bill-title">
		<h1>{{ text.paymentPart }}</h1>
	</div>

	<table id="qr-bill-content">
		<tr>
			<td id="qr-bill-left-column">
				<div id="qr-bill-scheme">
					{{ scheme-content }}
				</div>
				<div id="qr-bill-swiss-qr">
					<img src="{{ swiss-qr-image }}" id="qr-bill-swiss-qr-image">
				</div>
				<div id="qr-bill-amount">
					<div id="qr-bill-currency">
						{{ currency-content }}
					</div>
					<div id="qr-bill-amount">
						{{ amount-content }}
					</div>
				</div>
			</td>

			<td id="qr-bill-right-column">
				<div id="qr-bill-information">
					{{ information-content }}
				</div>
			</td>
		</tr>
	</table>
	
</div>
EOT;
}
