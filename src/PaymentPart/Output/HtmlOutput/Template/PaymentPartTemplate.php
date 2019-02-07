<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\Template;

class PaymentPartTemplate
{
    public const TEMPLATE = <<<EOT
<style>
#qr-bill {
	box-sizing: border-box;
	width: 210mm;
	height: 105mm;
	border: 0.2mm solid black;
	font-family: Arial, Frutiger, Helvetica, "Liberation Sans";
	border-collapse: collapse;
}

#qr-bill h1 {
	font-size: 11pt;
	font-weight: bold;
	margin: 0;
	padding: 0;
}

#qr-bill-receipt h1 {
	margin-bottom: 5mm;
}

#qr-bill h2 {
	font-weight: bold;
	margin: 0;
	margin-bottom: 1mm;
	margin-top: 2.5mm;
	padding: 0;
}

#qr-bill p {
	font-weight: normal;
	margin: 0;
	padding: 0;
}

#qr-bill-payment-part h2:first-child,
#qr-bill-receipt h2:first-child {
	margin-top: 0;
}

#qr-bill-receipt {
    box-sizing: border-box;
    width: 62mm;
	border-right: 0.2mm solid black;
	padding: 5mm;
	vertical-align: top;
}

#qr-bill-receipt p {
	font-size: 8pt;
}

#qr-bill-receipt h2 {
	font-size: 6pt;
}

#qr-bill-information-receipt {
    height: 51mm;
}

#qr-bill-acceptance-point {
    font-size: 8pt;
    font-weight: bold;
    text-align: right;
    margin-top: 5mm;
}

#qr-bill-payment-part {
    box-sizing: border-box;
    width: 148mm;
	padding: 5mm;
	vertical-align: top;
}

#qr-bill-payment-part p {
	font-size: 10pt;
}

#qr-bill-payment-part h2 {
	font-size: 8pt;
}

#qr-bill-payment-part-left {
    float: left;
}

#qr-bill-swiss-qr-image {
	width: 46mm;
	height: 46mm;
	margin: 5mm;
}

#qr-bill-amount {
	box-sizing: border-box;
	width: 46mm;
}

#qr-bill-currency {
	float: left;
	margin-right: 2mm;
}
</style>

<table id="qr-bill">
	<tr>
	    <td id="qr-bill-receipt">
	        <h1>{{ text.receipt }}</h1>
	        <div id="qr-bill-information-receipt">
                {{ information-content-receipt }}
            </div>
            <div id="qr-bill-amount">
                <div id="qr-bill-currency">
                    {{ currency-content }}
                </div>
                <div id="qr-bill-amount">
                    {{ amount-content }}
                </div>
            </div>
            <div id="qr-bill-acceptance-point">
                {{ text.acceptancePoint }}
            </div>
        </td>

        <td id="qr-bill-payment-part">
            <div id="qr-bill-payment-part-left">
                <h1>{{ text.paymentPart }}</h1>
                <img src="{{ swiss-qr-image }}" id="qr-bill-swiss-qr-image">
                <div id="qr-bill-amount">
                    <div id="qr-bill-currency">
                        {{ currency-content }}
                    </div>
                    <div id="qr-bill-amount">
                        {{ amount-content }}
                    </div>
                </div>
			</div>
			<div id="qr-bill-payment-part-right">
                <div id="qr-bill-information">
                    {{ information-content }}
                </div>
			</div>
        </td>
        
	</tr>
</table>
EOT;
}
