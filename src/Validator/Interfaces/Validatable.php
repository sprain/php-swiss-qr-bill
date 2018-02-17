<?php

namespace Sprain\SwissQrBill\Validator\Interfaces;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface Validatable
{
    public function getViolations() : ConstraintViolationListInterface;

    public function isValid() : bool;
}