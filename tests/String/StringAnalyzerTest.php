<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill\String;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\String\StringAnalyzer;

final class StringAnalyzerTest extends TestCase
{
    /**
     * @dataProvider separateWordsProvider
     */
    public function testGetSingleWords(string $string, array $expectedResult): void
    {
        $this->assertSame(
            $expectedResult,
            StringAnalyzer::getSingleWords($string)
        );
    }

    public function separateWordsProvider(): array
    {
        return [
            ['', ['']],
            ['Hello', ['Hello']],
            ['Hello world', ['Hello', 'world']],
            ['Hello world!', ['Hello', 'world!']],
            ['Hällø Ümläut!', ['Hällø', 'Ümläut!']],
            ['Hello world 123', ['Hello', 'world', '123']],
            ['Hello  world  123', ['Hello', 'world', '123']],
            ["Hello\nworld\n123", ['Hello', 'world', '123']],
            ["Hello\tworld\t123", ['Hello', 'world', '123']],
        ];
    }

    /**
     * @dataProvider countCharactersProvider
     */
    public function testCountCharacters(string $string, int $expectedResult): void
    {
        $this->assertSame(
            $expectedResult,
            StringAnalyzer::countCharacters($string)
        );
    }

    public function countCharactersProvider(): array
    {
        return [
            ['', 0],
            ['Hello', 5],
            ['Hello world', 11],
            ['Hello world 123', 15],
            ['Hällø Ümläut 123', 16],
            ["Hello\nworld\n123", 15],
            [" Hello\nworld\n123! ", 18],
        ];
    }
}