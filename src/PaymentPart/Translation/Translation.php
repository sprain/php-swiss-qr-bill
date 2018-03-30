<?php

namespace Sprain\SwissQrBill\PaymentPart\Translation;

class Translation
{
    private static function getAll() : array
    {
        return [
            'de' => [
                'paymentPart' => 'Zahlteil QR-Rechnung',
                'supports' => 'Unterstützt',
                'creditTransfer' => 'Überweisung',
                'account' => 'Konto',
                'creditor' => 'Zahlungsempfänger',
                'ultimateCreditor' => 'Endgültiger Zahlungsempfänger',
                'referenceNumber' => 'Referenznummer',
                'additionalInformation' => 'Zusätzliche Informationen',
                'debtor' => 'Zahlungspflichtiger',
                'dueDate' => 'Zahlbar bis',
                'currency' => 'Währung',
                'amount' => 'Betrag'
            ],

            'fr' => [
                'paymentPart' => 'Section paiement QR-facture',
                'supports' => 'Support',
                'creditTransfer' => 'Virement',
                'account' => 'Compte',
                'creditor' => 'Bénéficiaire',
                'ultimateCreditor' => 'Bénéficiaire final',
                'referenceNumber' => 'Numéro de référence',
                'additionalInformation' => 'Informations supplémentaires',
                'debtor' => 'Débiteur',
                'dueDate' => 'À payer jusqu\'au',
                'currency' => 'Monnaie',
                'amount' => 'Montant'
            ],

            'it' => [
                'paymentPart' => 'Sezione pagamento QR-fattura',
                'supports' => 'Sostiene',
                'creditTransfer' => 'Bonifico',
                'account' => 'Conto',
                'creditor' => 'Beneficiario',
                'ultimateCreditor' => 'Beneficiario finale',
                'referenceNumber' => 'Numero di riferimento',
                'additionalInformation' => 'Informazioni supplementari',
                'debtor' => 'Debitore',
                'dueDate' => 'Da pagare entro il',
                'currency' => 'Valuta',
                'amount' => 'Importo'
            ],

            'en' => [
                'paymentPart' => 'QR-bill payment part',
                'supports' => 'Supports',
                'creditTransfer' => 'Credit transfer',
                'account' => 'Account',
                'creditor' => 'Creditor',
                'ultimateCreditor' => 'Ultimate creditor',
                'referenceNumber' => 'Reference number',
                'additionalInformation' => 'Additional information',
                'debtor' => 'Debtor',
                'dueDate' => 'Due date',
                'currency' => 'Currency',
                'amount' => 'Amount'
            ]
        ];
    }

    public static function getAllByLanguage($language) :?array
    {
        $translations = self::getAll();
        if (array_key_exists($language, $translations)) {

            return $translations[$language];
        }
    }

    public static function get(string $key, string $language) : ?string
    {
        if ($translations = self::getAllByLanguage($language)) {
            if (array_key_exists($key, $translations)) {

                return $language[$key];
            }
        }
    }
}