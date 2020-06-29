<?php

namespace App\Contract\CommissionRatio;

interface CommissionRatioProvider
{
    /**
     * @param string $country
     * @return float
     * @throws CommissionRatioException
     */
    public function getCommissionRatio(string $country): float;
}