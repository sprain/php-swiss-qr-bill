<?php

namespace Sprain\SwissQrBill\Validator\Interfaces;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Mapping\ClassMetadataInterface;

interface SelfValidatable
{
    public function getViolations() : ConstraintViolationListInterface;

    public function isValid() : bool;

    public static function loadValidatorMetadata(ClassMetadataInterface $metadata) : void;
}