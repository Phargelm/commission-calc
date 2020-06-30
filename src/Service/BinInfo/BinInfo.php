<?php

namespace App\Service\BinInfo;

use \App\Contract\BinInfo\BinInfo as BinInfoContract;

/**
 * Class BinInfo
 * @package App\Service\BinInfo
 *
 * Only several parameters of bin information are described here, but they can be extended if necessary.
 * Currently only country name is used.
 */
class BinInfo implements BinInfoContract
{
    private $bankName;
    private $country;
    private $isPrepaid;

    public function __construct(string $country, ?string $bankName = null, ?bool $isPrepaid = null)
    {
        $this->bankName = $bankName;
        $this->country = $country;
        $this->isPrepaid = $isPrepaid;
    }

    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function isPrepaid(): ?bool
    {
        return $this->isPrepaid;
    }
}