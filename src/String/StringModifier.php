<?php

namespace Sprain\SwissQrBill\String;

class StringModifier
{
    public static function replaceLineBreaksWithString(?string $string): string
    {
        return str_replace(array("\r", "\n"), ' ', $string);
    }

    public static function replaceMultipleSpacesWithOne(?string $string): string
    {
        return preg_replace('/ +/', ' ', $string);
    }
}
