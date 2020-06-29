<?php

namespace App\Contract\Exchange;

use Money\Currency;
use Money\Money;

interface ExchangeService
{
    /**
     * @param Money $money
     * @param Currency $baseCurrency
     * @return Money
     * @throws ExchangeException
     */
    public function exchangeMoney(Money $money, Currency $baseCurrency): Money;
}