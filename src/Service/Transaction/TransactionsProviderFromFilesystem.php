<?php

namespace App\Service\Transaction;

use App\Contract\Transaction\TransactionsProvider;
use Money\MoneyParser;

class TransactionsProviderFromFilesystem implements TransactionsProvider
{
    private $fileName;
    private $moneyParser;

    public function __construct(string $fileName, MoneyParser $moneyParser)
    {
        $this->fileName = $fileName;
        $this->moneyParser = $moneyParser;
    }

    /**
     * @inheritDoc
     */
    public function getTransactionsList(): array
    {
        $rawTransactions = file($this->fileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($rawTransactions === false) {
            throw new TransactionException('Error is occurred during reading transactions from file');
        }
        $transactionsList = [];
        foreach ($rawTransactions as $rawTransaction) {

            $parsedTransaction = json_decode($rawTransaction, true);

            if ($parsedTransaction === null || empty($parsedTransaction['bin']) ||
            empty($parsedTransaction['amount']) || empty($parsedTransaction['currency'])) {
                throw new TransactionException('Error is occurred during decoding transactions');
            }

            $transactionsList[] = new Transaction(
                $parsedTransaction['bin'],
                $this->moneyParser->parse($parsedTransaction['amount'], $parsedTransaction['currency'])
            );
        }

        return $transactionsList;
    }
}