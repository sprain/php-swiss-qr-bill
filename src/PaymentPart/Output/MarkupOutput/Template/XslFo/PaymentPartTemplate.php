<?php

namespace Sprain\SwissQrBill\PaymentPart\Output\MarkupOutput\Template\XslFo;

class PaymentPartTemplate
{
    public const TEMPLATE = <<<EOT
<fo:block height="105mm" start-indent="0" end-indent="0" line-height="1pc" keep-together="always">
<fo:table width="210mm" table-layout="fixed" border="1pt solid black" margin-top="0pc">
<fo:table-column column-number="1" column-width="62mm" border="1pt solid blue" />
<fo:table-column column-number="2" column-width="56mm" border="1pt solid green" />
<fo:table-column column-number="3" column-width="92mm" border="1pt solid red" />
<fo:table-body>
    <fo:table-row>
        <fo:table-cell column-number="1">
            <fo:block font-family="Arial, Frutiger, Helvetica, Liberation Sans" font-weight="bold" font-size="11pt" line-height="normal" linefeed-treatment="preserve" margin-top="5mm" start-indent="5mm">
                {{ text.receipt }}
            </fo:block>
        </fo:table-cell>
        <fo:table-cell column-number="2">
            <fo:block font-family="Arial, Frutiger, Helvetica, Liberation Sans" font-weight="bold" font-size="11pt" line-height="normal" linefeed-treatment="preserve" margin-top="5mm" start-indent="5mm">
                {{ text.paymentPart }}
            </fo:block>
            <fo:block font-family="MiloB_TTF" margin-top="5mm" start-indent="5mm">
                <!-- Here comes the QR code as data-uri / base64 string -->
                <fo:external-graphic src="{{ swiss-qr-image }}" content-height="46mm" content-width="46mm" />
            </fo:block>
        </fo:table-cell>
        <fo:table-cell column-number="3"><fo:block /></fo:table-cell>
    </fo:table-row>
    <fo:table-row>
        <fo:table-cell column-number="3">
            <fo:block font-family="OcrB_TTF" font-size="11pt" font-weight="normal" letter-spacing="0pt" text-align="right" margin-right="8mm">
                scösdcklöshdlkc
            </fo:block>
        </fo:table-cell>
    </fo:table-row>
    <fo:table-row>
        <fo:table-cell column-number="1">
            <fo:block  font-size="10pt" font-weight="bold" start-indent="1.2in">01-57518-1</fo:block>
        </fo:table-cell>
        <fo:table-cell column-number="2">
            <fo:block  font-size="10pt" font-weight="bold" start-indent="1.2in">01-57518-1</fo:block>
        </fo:table-cell>
    </fo:table-row>
    <fo:table-row>
        <fo:table-cell column-number="1">
            <fo:block font-family="Courier" font-size="12pt" font-weight="bold" letter-spacing="1pt" text-align="right" margin-right="6mm" margin-top="14.4pt">
                sdlckjsdckj
            </fo:block>
        </fo:table-cell>
        <fo:table-cell column-number="2">
            <fo:block font-family="Courier" font-size="12pt" font-weight="bold" letter-spacing="1pt" text-align="right" margin-right="6mm" margin-top="14.4pt">
                yljuchglkdjch,lchj
            </fo:block>
        </fo:table-cell>
        <fo:table-cell column-number="3">
            <fo:block-container width="80mm" overflow="hidden" wrap-option="no-wrap">
                <fo:block font-family="OcrB_TTF" font-size="1pc" letter-spacing="-1pt" line-height="normal" linefeed-treatment="preserve" start-indent="0.3in" margin-top="0.1in">
                    cdsacsdcghv
                </fo:block>
            </fo:block-container>
        </fo:table-cell>
    </fo:table-row>
    <fo:table-row>
        <fo:table-cell column-number="1">
            <fo:block-container width="52.38mm" overflow="hidden" wrap-option="no-wrap">
                <fo:block font-family="OcrB_TTF" font-size="0.65pc" letter-spacing="-1pt" line-height="normal" linefeed-treatment="preserve" start-indent="0.3in" margin-top="4pt">
                    ,hjcd,jhcd
                </fo:block>
                <fo:block font-family="OcrB_TTF" font-size="0.7pc" letter-spacing="-1pt" line-height="normal" linefeed-treatment="preserve" start-indent="0.3in" margin-top="4pt">
                    kasdgkjagdkjasgh
                    <fo:block />
                </fo:block>
            </fo:block-container>
        </fo:table-cell>
    </fo:table-row>
    <fo:table-row>
        <fo:table-cell column-number="2" number-columns-spanned="2">
            <fo:block font-family="OcrB_TTF" font-size="1pc" text-align="right" margin-right="7.7mm" margin-top="1pc">esr bottom code</fo:block>
        </fo:table-cell>
    </fo:table-row>
</fo:table-body>
</fo:table>
</fo:block>
EOT;
}
