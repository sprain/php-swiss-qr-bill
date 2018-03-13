<?php

namespace Sprain\SwissQrBill\Tests\String;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\String\StringCleaner;

class StringCleanerTest extends TestCase
{
    /**
     * @dataProvider lineBreaksProvider
     */
    public function testRemoveLineBreaks($string, $expectedResult)
    {
        $this->assertSame(
            $expectedResult,
            StringCleaner::replaceLineBreaksWithString($string)
        );
    }

    public function lineBreaksProvider()
    {
        return [
            ["foo\nbar\rbaz\r\n", "foo bar baz  "],
            ["\n\nfoo\nbar\nbaz\n\n", "  foo bar baz  "],
            ["\n\nfoo\n\nbar\n\nbaz\n\n", "  foo  bar  baz  "],
        ];
    }

    /**
     * @dataProvider multipleSpacesProvider
     */
    public function testReplaceMultipleSpacesWithOne($string, $expectedResult)
    {
        $this->assertSame(
            $expectedResult,
            StringCleaner::replaceMultipleSpacesWithOne($string)
        );
    }

    public function multipleSpacesProvider()
    {
        return [
            [" foo bar baz ", " foo bar baz "],
            ["  foo  bar  baz  ", " foo bar baz "],
            ["foo  bar  baz", "foo bar baz"],
        ];
    }
}