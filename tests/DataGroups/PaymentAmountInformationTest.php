<?php

namespace Sprain\SwissQrBill\Tests\DataGroups;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroups\PaymentAmountInformation;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentAmountInformationTest extends TestCase
{
    /** @var  ValidatorInterface */
    private $validator;

    /** @var PaymentAmountInformation */
    private $paymentAmountInformation;

    public function setUp()
    {
        $this->validator = Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata')
            ->getValidator();

        // Valid default to be adjusted in single tests
        $this->paymentAmountInformation = (new PaymentAmountInformation())
            ->setAmount(25)
            ->setCurrency('CHF')
            ->setDueDate(new \DateTime('+30 days'));
    }

    /**
     * @dataProvider validAmountProvider
     */
    public function testAmountIsValid($value)
    {
        $this->paymentAmountInformation->setAmount($value);

        $this->assertSame(0, $this->validator->validate($this->paymentAmountInformation)->count());
    }

    public function validAmountProvider()
    {
        return [
            [0],
            [11.11],
            [100.2],
            [999999999.99],
        ];
    }

    /**
     * @dataProvider invalidAmountProvider
     */
    public function testAmountIsInvalid($value)
    {
        $this->paymentAmountInformation->setAmount($value);

        $this->assertSame(1, $this->validator->validate($this->paymentAmountInformation)->count());
    }

    public function invalidAmountProvider()
    {
        return [
            [-0.01],
            [1999999999.99],
            // [11.111], @todo: only two decimal places should be allowed
        ];
    }
}