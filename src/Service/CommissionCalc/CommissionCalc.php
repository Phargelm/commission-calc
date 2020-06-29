<?php

namespace App\Service\CommissionCalc;

use App\Contract\BinInfo\BinInfoException;
use App\Contract\BinInfo\BinInfoProvider;
use App\Contract\CommissionCalc\CommissionCalc as CommissionCalcContract;
use App\Contract\CommissionRatio\CommissionRatioException;
use App\Contract\CommissionRatio\CommissionRatioProvider;
use App\Contract\Exchange\ExchangeException;
use App\Contract\Exchange\ExchangeService;
use App\Contract\Transaction\Transaction;
use Money\Currency;
use Money\Money;

class CommissionCalc implements CommissionCalcContract
{
    private $binInfoProvider;
    private $exchangeService;
    private $commissionRatioProvider;

    private $baseCurrency;

    public function __construct(
        BinInfoProvider $binInfoProvider,
        ExchangeService $exchangeService,
        CommissionRatioProvider $commissionRatioProvider,
        Currency $baseCurrency
    ) {
        $this->binInfoProvider = $binInfoProvider;
        $this->exchangeService = $exchangeService;
        $this->commissionRatioProvider = $commissionRatioProvider;
        $this->baseCurrency = $baseCurrency;
    }

    /**
     * @inheritDoc
     */
    public function calculateCommission(Transaction $transaction): Money
    {
        try {
            $binInfo = $this->binInfoProvider->getBinInfo($transaction->getBin());
            $commissionRatio = $this->commissionRatioProvider->getCommissionRatio($binInfo->getCountry());
            $baseMoney = $transaction->getMoney();
            if (!$baseMoney->getCurrency()->equals($this->baseCurrency)) {
                $baseMoney = $this->exchangeService->exchangeMoney($baseMoney, $this->baseCurrency);
            }
            return $baseMoney->multiply($commissionRatio, Money::ROUND_UP);

        } catch (BinInfoException $exception) {
            throw new CommissionCalcException('Error is occurred during retrieving bin info', 0, $exception);
        } catch (CommissionRatioException $exception) {
            throw new CommissionCalcException('Error is occurred during retrieving commission ratio', 0, $exception);
        } catch (ExchangeException $exception) {
            throw new CommissionCalcException('Error is occurred during exchange currencies', 0, $exception);
        }
    }
}