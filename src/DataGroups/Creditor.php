<?php

namespace Sprain\SwissQrBill\DataGroups;

use Sprain\SwissQrBill\Traits\PersonTrait;
use Sprain\SwissQrBill\DataGroups\Interfaces\PersonInterface;

class Creditor implements PersonInterface
{
    use PersonTrait;
}