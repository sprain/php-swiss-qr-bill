<?php

namespace Sprain\SwissQrBill\String;

class StringCleaner
{
    public static function removeLineBreaks($string) : string
    {
        return str_replace(array("\r", "\n"), ' ', $string);
    }

    public static function removeMultipleSpaces($string) : string
    {
        return preg_replace('/ +/', ' ', $string);
    }
}