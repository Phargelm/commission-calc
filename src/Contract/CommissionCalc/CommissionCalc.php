<?php

namespace App\Contract\CommissionCalc;

use App\Contract\Transaction\Transaction;
use Money\Money;

interface CommissionCalc
{
    /**
     * @param Transaction $transaction
     * @return Money
     * @throws CommissionCalcException
     */
    public function calculateCommission(Transaction $transaction): Money;
}