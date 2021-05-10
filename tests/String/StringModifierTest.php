<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill\String;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\String\StringModifier;

final class StringModifierTest extends TestCase
{
    /**
     * @dataProvider lineBreaksProvider
     */
    public function testRemoveLineBreaks(?string $string, string $expectedResult): void
    {
        $this->assertSame(
            $expectedResult,
            StringModifier::replaceLineBreaksWithString($string)
        );
    }

    public function lineBreaksProvider(): array
    {
        return [
            [null, ''],
            ["foo\nbar\rbaz\r\n", "foo bar baz  "],
            ["\n\nfoo\nbar\nbaz\n\n", "  foo bar baz  "],
            ["\n\nfoo\n\nbar\n\nbaz\n\n", "  foo  bar  baz  "],
        ];
    }

    /**
     * @dataProvider multipleSpacesProvider
     */
    public function testReplaceMultipleSpacesWithOne(?string $string, string $expectedResult): void
    {
        $this->assertSame(
            $expectedResult,
            StringModifier::replaceMultipleSpacesWithOne($string)
        );
    }

    public function multipleSpacesProvider(): array
    {
        return [
            [null, ''],
            [" foo bar baz ", " foo bar baz "],
            ["  foo  bar  baz  ", " foo bar baz "],
            ["foo  bar  baz", "foo bar baz"],
        ];
    }

    /**
     * @dataProvider stripWhitespaceProvider
     */
    public function testStripWhitespace(?string $string, string $expectedResult): void
    {
        $this->assertSame(
            $expectedResult,
            StringModifier::stripWhitespace($string)
        );
    }

    public function stripWhitespaceProvider(): array
    {
        return [
            [null, ''],
            ['1 ', '1'],
            [' 2', '2'],
            [' foo ', 'foo'],
            ['   foo   ', 'foo'],
            ['   foo   bar   ', 'foobar'],
        ];
    }
}