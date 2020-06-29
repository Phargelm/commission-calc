<?php

namespace App\Contract\BinInfo;

/**
 * Interface BinInfo
 * @package App\Contract
 * Only some possible information that can be retrieved by bin is defined. This interface can be extended if it needed.
 */
interface BinInfo
{
    public function getBankName(): ?string;

    public function getCountry(): string;

    public function isPrepaid(): ?bool;
}