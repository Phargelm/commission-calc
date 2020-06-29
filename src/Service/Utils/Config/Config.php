<?php

namespace App\Service\Utils\Config;

use Money\Currency;

class Config
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getEuCountriesList(): array
    {
        if (isset($this->config['euCountries'])) {
            return $this->config['euCountries'];
        }

        throw new ConfigException('EU countries list is not found in config');
    }

    /**
     * @return float
     * @throws ConfigException
     */
    public function getCommissionRatio(): float
    {
        if (isset($this->config['commissions']['others'])) {
            return $this->config['commissions']['others'];
        }

        throw new ConfigException('Commission ratio is not found in config');
    }

    /**
     * @return float
     * @throws ConfigException
     */
    public function getCommissionsRatioForEu(): float
    {
        if (isset($this->config['commissions']['eu'])) {
            return $this->config['commissions']['eu'];
        }

        throw new ConfigException('Commission ratio for EU is not found in config');
    }

    /**
     * @return Currency
     * @throws ConfigException
     */
    public function getBaseCurrency(): Currency
    {
        if (isset($this->config['baseCurrency'])) {
            return new Currency($this->config['baseCurrency']);
        }

        throw new ConfigException('Base currency is not found in config');
    }
}