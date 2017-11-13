<?php

namespace Sprain\SwissQrBill\DataGroups;

use Sprain\SwissQrBill\Traits\PersonTrait;
use Sprain\SwissQrBill\DataGroups\Interfaces\PersonInterface;

class UltimateDebtor implements PersonInterface
{
    use PersonTrait;
}