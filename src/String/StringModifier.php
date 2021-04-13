<?php declare(strict_types=1);

namespace Sprain\SwissQrBill\String;

final class StringModifier
{
    public static function replaceLineBreaksWithString(?string $string): string
    {
        return str_replace(["\r", "\n"], ' ', $string);
    }

    public static function replaceMultipleSpacesWithOne(?string $string): string
    {
        return preg_replace('/ +/', ' ', $string);
    }

    public static function stripWhitespace(?string $string): string
    {
        return preg_replace('/\s+/', '', $string);
    }
}
