<?php

use App\Contract\CommissionCalc\CommissionCalc;
use App\Contract\Transaction\TransactionsProvider;
use App\Service\Core;
use App\Service\Transaction\Transaction;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class CoreTest extends TestCase
{
    private $core;
    private $commissions;

    public function setUp(): void
    {
        /** amounts (10000, 5000) are provided in minor units */
        $transactions = [
            new Transaction('54120782', new Money(10000, new Currency('DKK'))),
            new Transaction('51487497', new Money(5000, new Currency('HRK'))),
        ];

        $this->commissions = [
            new Money(100, new Currency('EUR')),
            new Money(50, new Currency('EUR')),
        ];

        $transactionProviderStub = $this->createStub(TransactionsProvider::class);
        $transactionProviderStub->method('getTransactionsList')
            ->willReturn($transactions);

        $commissionCalcStub = $this->createStub(CommissionCalc::class);
        $commissionCalcStub->method('calculateCommission')
            ->willReturnMap([
                [$transactions[0], $this->commissions[0]],
                [$transactions[1], $this->commissions[1]],
            ]);

        $this->core = new Core($transactionProviderStub, $commissionCalcStub);
    }

    public function testCalculate()
    {
        $this->assertEquals($this->commissions, $this->core->calculate());
    }
}