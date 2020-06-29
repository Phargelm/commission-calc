<?php

namespace App\Service\Transaction;

use \App\Contract\Transaction\Transaction as TransactionContract;
use Money\Money;

class Transaction implements TransactionContract
{
    private $bin;
    private $money;

    public function __construct(string $bin, Money $money)
    {
        $this->bin = $bin;
        $this->money = $money;
    }

    public function getBin(): string
    {
        return $this->bin;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }
}