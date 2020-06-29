<?php

namespace App\Service\CommissionRatio;

use App\Service\Utils\Config\Config;
use App\Contract\CommissionRatio\CommissionRatioProvider;
use App\Service\Utils\Config\ConfigException;

class CommissionRatioFromConfig implements CommissionRatioProvider
{
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function getCommissionRatio(string $country): float
    {
        try {
            $euCountries = $this->config->getEuCountriesList();
            if (array_search($country, $euCountries) !== false) {
                return $this->config->getCommissionsRatioForEu();
            }
            return $this->config->getCommissionRatio();

        } catch (ConfigException $exception) {
            throw new CommissionRatioException('Error is occurred during retrieving data from config', 0, $exception);
        }
    }
}