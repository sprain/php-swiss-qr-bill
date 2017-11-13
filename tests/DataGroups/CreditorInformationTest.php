<?php

namespace Sprain\SwissQrBill\Tests\DataGroups;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroups\CreditorInformation;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreditorInformationTest extends TestCase
{
    /** @var  ValidatorInterface */
    private $validator;

    public function setUp()
    {
        $this->validator = Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata')
            ->getValidator();
    }

    /**
     * @dataProvider validIbanProvider
     */
    public function testIbanIsValid($value)
    {
        $header = new CreditorInformation();
        $header->setIban($value);

        $this->assertSame(0, $this->validator->validate($header)->count());
    }

    public function validIbanProvider()
    {
        return [
            ['CH93 0076 2011 6238 5295 7'],
            ['CH9300762011623852957'],
            ['LI21 0881 0000 2324 013A A'],
            ['LI21088100002324013AA'],
        ];
    }

    /**
     * @dataProvider invalidIbanProvider
     */
    public function testIbanIsInvalid($value, $numberOfViolations)
    {
        $header = new CreditorInformation();
        $header->setIban($value);

        $this->assertSame($numberOfViolations, $this->validator->validate($header)->count());
    }

    public function invalidIbanProvider()
    {
        return [

            // missing number at end
            ['CH93 0076 2011 6238 5295', 1],
            ['CH930076201162385295', 1],
            ['LI21 0881 0000 2324 013A', 1],
            ['LI21088100002324013A', 1],

            // missing letter in front
            ['H93 0076 2011 6238 5295', 2],
            ['H930076201162385295', 2],
            ['I21 0881 0000 2324 013A', 2],
            ['I21088100002324013A', 2],

            // valid IBANs from unsupported countries
            ['AT61 1904 3002 3457 3201', 1],
            ['NO9386011117947', 1],

            // random strings
            ['foo', 2],
            ['123', 2],
            ['*', 2]
        ];
    }

    public function testIbanIsRequired()
    {
        $header = new CreditorInformation();

        $this->assertSame(1, $this->validator->validate($header)->count());
    }
}