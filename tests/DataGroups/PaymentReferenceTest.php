<?php

namespace Sprain\SwissQrBill\Tests\DataGroups;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\DataGroups\PaymentAmountInformation;
use Sprain\SwissQrBill\DataGroups\PaymentReference;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentReferenceTest extends TestCase
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
    }

    public function testValidQrReference()
    {
        $paymentReference = new PaymentReference();
        $paymentReference->setType(PaymentReference::TYPE_QR);
        $paymentReference->setReference('012345678901234567890123456');

        $this->assertSame(0, $this->validator->validate($paymentReference)->count());
    }

    /**
     * @dataProvider invalidQrReferenceProvider
     */
    public function testInvalidQrReference($value)
    {
        $paymentReference = new PaymentReference();
        $paymentReference->setType(PaymentReference::TYPE_QR);
        $paymentReference->setReference($value);

        $this->assertSame(1, $this->validator->validate($paymentReference)->count());
    }

    public function invalidQrReferenceProvider()
    {
        return [
            ['01234567890123456789012345'],   // too short
            ['0123456789012345678901234567'], // too long
            ['Ä12345678901234567890123456']   // invalid characters
        ];
    }

    public function testValidScorReference()
    {
        $paymentReference = new PaymentReference();
        $paymentReference->setType(PaymentReference::TYPE_SCOR);
        $paymentReference->setReference('RF18539007547034');

        $this->assertSame(0, $this->validator->validate($paymentReference)->count());
    }

    /**
     * @dataProvider invalidScorReferenceProvider
     */
    public function testInvalidScorReference($value)
    {
        $paymentReference = new PaymentReference();
        $paymentReference->setType(PaymentReference::TYPE_SCOR);
        $paymentReference->setReference($value);

        $this->assertSame(1, $this->validator->validate($paymentReference)->count());
    }

    public function invalidScorReferenceProvider()
    {
        return [
            ['RF12'],// too short
            ['RF181234567890123456789012'], // too long
            ['RF1853900754703Ä']  // invalid characters
        ];
    }
}