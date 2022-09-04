<?php

use Sprain\SwissQrBill\String\StringAnalyzer;
use Sprain\SwissQrBill\String\StringModifier;

namespace Sprain\SwissQrBill\DataGroup\Element\Abstracts;

abstract class Address
{
    protected static function normalizeString(?string $string): ?string
    {
        if (is_null($string)) {
            return null;
        }

        $string = trim($string);
        $string = StringModifier::replaceLineBreaksAndTabsWithSpace($string);
        $string = StringModifier::replaceMultipleSpacesWithOne($string);

        return $string;
    }

    protected static function clearMultilines(array $lines): array
    {
        $noOfLongLines = 0;

        foreach($lines as $line) {
            if (self::willBeMoreThanOneLineOnReceipt($line)) {
                $noOfLongLines++;
            }
        }

        if (0 < $noOfLongLines) {
            if (isset($lines[2])) {
                unset($lines[2]);
            }
        }

        if (2 == $noOfLongLines) {
            unset($lines[3]);
        }

        return $lines;
    }

    private static function willBeMoreThanOneLineOnReceipt(string $string): bool
    {
        $words = StringAnalyzer::getSingleWords($string);
        $count = 0;

        foreach($words as $word) {
            $count += StringAnalyzer::countCharacters($word);
            $count++;

            if ($count > 40) {
                return true;
            }
        }

        return false;
    }
}