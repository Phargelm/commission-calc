<?php

namespace App\Service;

use App\Contract\CommissionCalc\CommissionCalc;
use App\Contract\CommissionCalc\CommissionCalcException;
use App\Contract\Transaction\TransactionException;
use App\Contract\Transaction\TransactionsProvider;
use Money\Money;

class Core
{
    private $transactionsProvider;
    private $commissionCalc;

    public function __construct(TransactionsProvider $transactionsProvider, CommissionCalc $commissionCalc)
    {
        $this->transactionsProvider = $transactionsProvider;
        $this->commissionCalc = $commissionCalc;
    }

    /**
     * @return Money[]
     * @throw AppException
     */
    public function calculate(): array
    {
        $commissions = [];
        try {
            $transactions = $this->transactionsProvider->getTransactionsList();
            foreach ($transactions as $transaction) {
                $commissions[] = $this->commissionCalc->calculateCommission($transaction);
            }
        } catch (TransactionException $exception) {
            throw new AppException('Error is occured during transactions list retrieving', 0, $exception);
        } catch (CommissionCalcException $exception) {
            throw new AppException('Error is occured during commission calculation', 0, $exception);
        }
        return $commissions;
    }
}