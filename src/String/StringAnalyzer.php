<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\String;

/**
 * @internal
 */
final class StringAnalyzer
{
    public static function getSingleWords(string $string): array
    {
        return preg_split('/\s+/', $string);
    }

    public static function countCharacters(string $string): int
    {
        return mb_strlen($string);
    }
}
