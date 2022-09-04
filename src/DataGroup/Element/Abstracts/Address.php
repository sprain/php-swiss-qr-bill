<?php

namespace Sprain\SwissQrBill\DataGroup\Element\Abstracts;

use Sprain\SwissQrBill\String\StringAnalyzer;
use Sprain\SwissQrBill\String\StringModifier;

abstract class Address
{
    private const MAX_CHARS_PER_LINE_ON_RECEIPT = 40;

    protected static function normalizeString(?string $string): ?string
    {
        if (is_null($string)) {
            return null;
        }

        $string = trim($string);
        $string = StringModifier::replaceLineBreaksAndTabsWithSpaces($string);
        $string = StringModifier::replaceMultipleSpacesWithOne($string);

        return $string;
    }

    protected static function clearMultilines(array $lines): array
    {
        $noOfLongLines = 0;

        foreach ($lines as $line) {
            if (self::willBeMoreThanOneLineOnReceipt($line)) {
                $noOfLongLines++;
            }
        }

        if (0 < $noOfLongLines) {
            if (isset($lines[2])) {
                unset($lines[2]);
            }
        }

        if (1 < $noOfLongLines) {
            unset($lines[3]);
        }

        return $lines;
    }

    private static function willBeMoreThanOneLineOnReceipt(string $string): bool
    {
        $words = StringAnalyzer::getSingleWords($string);
        $count = 0;

        foreach ($words as $word) {
            $count += StringAnalyzer::countCharacters($word);
            $count++;

            if ($count > self::MAX_CHARS_PER_LINE_ON_RECEIPT) {
                return true;
            }
        }

        return false;
    }
}
