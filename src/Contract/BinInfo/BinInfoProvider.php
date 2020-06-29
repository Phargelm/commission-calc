<?php

namespace App\Contract\BinInfo;

interface BinInfoProvider
{
    /**
     * @param string $bin
     * @return BinInfo
     * @throws BinInfoException
     */
    public function getBinInfo(string $bin): BinInfo;
}