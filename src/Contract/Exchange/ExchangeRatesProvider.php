<?php

namespace App\Contract\Exchange;

use App\Service\Exchange\ExchangeException;
use Money\Currency;
use Money\Exchange;

interface ExchangeRatesProvider
{
    /**
     * @param Currency $quoteCurrency
     * @param Currency $baseCurrency
     * @return float
     * @throws ExchangeException
     */
    public function getExchangeRate(Currency $quoteCurrency, Currency $baseCurrency): Exchange;
}