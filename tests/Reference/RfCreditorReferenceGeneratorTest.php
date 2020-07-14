<?php

namespace Sprain\Tests\SwissQrBill\Reference;

use Sprain\SwissQrBill\Reference\RfCreditorReferenceGenerator;
use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\String\StringModifier;

class RfCreditorReferenceGeneratorTest extends TestCase
{
    /**
     * @dataProvider rfCreditorReferenceProvider
     */
    public function testMakesResultsViaConstructor($input)
    {
        $generator = new RfCreditorReferenceGenerator($input);

        $output = $generator->doGenerate();

        $this->assertStringContainsStringIgnoringCase(
            StringModifier::stripWhitespace($input),
            StringModifier::stripWhitespace($output)
        );
    }

    /**
     * @dataProvider rfCreditorReferenceProvider
     */
    public function testMakesResultsViaFacade($input)
    {
        $output = RfCreditorReferenceGenerator::generate($input);

        $this->assertStringContainsStringIgnoringCase(
            StringModifier::stripWhitespace($input),
            StringModifier::stripWhitespace($output)
        );
    }

    public function rfCreditorReferenceProvider()
    {
        return [
            ['1'],
            ['a'],
            ['B'],
            ['aBcD eFgH iJkL mNoP qR12 3'],
        ];
    }

    /**
     * @dataProvider invalidReferenceProvider
     */
    public function testInvalidReference($input)
    {
        $generator = new RfCreditorReferenceGenerator($input);

        $this->assertCount(1, $generator->getViolations());
    }

    public function invalidReferenceProvider()
    {
        return [
            ['aBcD eFgH iJkL mNoP qR12 34'], // to long
            [''], // to short
            ['123ä'], // invalid letter
            ['123.'], // invalid symbol
        ];
    }

}