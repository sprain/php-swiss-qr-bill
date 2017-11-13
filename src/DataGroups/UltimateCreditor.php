<?php

namespace Sprain\SwissQrBill\DataGroups;

use Sprain\SwissQrBill\Traits\PersonTrait;
use Sprain\SwissQrBill\DataGroups\Interfaces\PersonInterface;

class UltimateCreditor implements PersonInterface
{
    use PersonTrait;
}