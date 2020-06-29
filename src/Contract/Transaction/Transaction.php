<?php

namespace App\Contract\Transaction;

use Money\Money;

interface Transaction
{
    public function getBin(): string;

    public function getMoney(): Money;
}