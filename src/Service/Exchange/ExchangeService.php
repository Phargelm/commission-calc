<?php

namespace App\Service\Exchange;

use App\Contract\Exchange\ExchangeRatesProvider;
use \App\Contract\Exchange\ExchangeService as ExchangeServiceContract;
use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;

class ExchangeService implements ExchangeServiceContract
{
    private $exchangeRatesProvider;

    public function __construct(ExchangeRatesProvider $exchangeRatesProvider)
    {
        $this->exchangeRatesProvider = $exchangeRatesProvider;
    }

    /**
     * @inheritDoc
     */
    public function exchangeMoney(Money $money, Currency $baseCurrency): Money
    {
        $exchange = $this->exchangeRatesProvider->getExchangeRate($money->getCurrency(), $baseCurrency);
        $converter = new Converter(new ISOCurrencies(), $exchange);
        return $converter->convert($money, $baseCurrency, Money::ROUND_UP);
    }
}
