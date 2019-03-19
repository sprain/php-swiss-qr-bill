<?php

namespace Sprain\SwissQrBill\PaymentPart\Translation;

class Translation
{
    private const TRANSLATIONS = [
        'de' => [
            'paymentPart' => 'Zahlteil',
            'creditor' => 'Konto / Zahlbar an',
            'reference' => 'Referenz',
            'additionalInformation' => 'Zusätzliche Informationen',
            'currency' => 'Währung',
            'amount' => 'Betrag',
            'receipt' => 'Empfangsschein',
            'acceptancePoint' => 'Annahmestelle',
            'separate' => 'Vor der Einzahlung abzutrennen',
            'payableBy' => 'Zahlbar durch',
            'payableByName' => 'Zahlbar durch (Name/Adresse)',
            'inFavorOf' => 'Zugunsten'
        ],

        'fr' => [
            'paymentPart' => 'Section paiement',
            'creditor' => 'Compte / Payable à',
            'reference' => 'Référence',
            'additionalInformation' => 'Informations supplémentaires',
            'currency' => 'Monnaie',
            'amount' => 'Montant',
            'receipt' => 'Récépissé',
            'acceptancePoint' => 'Point de dépôt',
            'separate' => 'A détacher avant le versement',
            'payableBy' => 'Payable par',
            'payableByName' => 'Payable par (nom/adresse)',
            'inFavorOf' => 'En faveur de'
        ],

        'it' => [
            'paymentPart' => 'Sezione pagamento',
            'creditor' => 'Conto / Pagabile a',
            'reference' => 'Riferimento',
            'additionalInformation' => 'Informazioni supplementari',
            'currency' => 'Valuta',
            'amount' => 'Importo',
            'receipt' => 'Ricevuta',
            'acceptancePoint' => 'Punto di accettazione',
            'separate' => 'Da staccare prima del versamento',
            'payableBy' => 'Pagabile da',
            'payableByName' => 'Pagabile da (nome/indirizzo)',
            'inFavorOf' => 'A favore di'
        ],

        'en' => [
            'paymentPart' => 'Payment part',
            'creditor' => 'Account / Payable to',
            'reference' => 'Reference',
            'additionalInformation' => 'Additional information',
            'currency' => 'Currency',
            'amount' => 'Amount',
            'receipt' => 'Receipt',
            'acceptancePoint' => 'Acceptance point',
            'separate' => 'Separate before paying in',
            'payableBy' => 'Payable by',
            'payableByName' => 'Payable by (name/address)',
            'inFavorOf' => 'In favour of'
        ]
    ];

    public static function getAllByLanguage($language): ?array
    {
        if (array_key_exists($language, self::TRANSLATIONS)) {

            return self::TRANSLATIONS[$language];
        }
    }

    public static function get(string $key, string $language): ?string
    {
        if ($translations = self::getAllByLanguage($language)) {
            if (array_key_exists($key, $translations)) {

                return $translations[$key];
            }
        }
    }
}