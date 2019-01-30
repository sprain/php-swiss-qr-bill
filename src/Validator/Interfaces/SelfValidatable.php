<?php

namespace Sprain\SwissQrBill\Validator\Interfaces;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface SelfValidatable
{
    public function getViolations() : ConstraintViolationListInterface;

    public function isValid() : bool;
}