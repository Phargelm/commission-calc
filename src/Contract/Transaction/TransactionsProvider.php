<?php

namespace App\Contract\Transaction;

interface TransactionsProvider
{
    /**
     * @return Transaction[]
     * @throws TransactionException
     */
    public function getTransactionsList(): array;
}