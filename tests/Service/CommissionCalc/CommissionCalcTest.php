<?php

use App\Contract\BinInfo\BinInfoProvider;
use App\Contract\CommissionRatio\CommissionRatioProvider;
use App\Contract\Exchange\ExchangeService;
use App\Service\BinInfo\BinInfo;
use App\Service\CommissionCalc\CommissionCalc;
use App\Service\Transaction\Transaction;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class CommissionCalcTest extends TestCase
{
    private $binInfoProviderStub;
    private $commissionRatioProviderStub;
    private $exchangeServiceStub;
    private $commissionCalc;
    private $baseCurrency;

    public function setUp(): void
    {
        $this->baseCurrency = new Currency('EUR');
        $this->binInfoProviderStub = $this->createStub(BinInfoProvider::class);
        $this->commissionRatioProviderStub = $this->createStub(CommissionRatioProvider::class);
        $this->exchangeServiceStub = $this->createMock(ExchangeService::class);

        $this->commissionCalc = new CommissionCalc(
            $this->binInfoProviderStub,
            $this->exchangeServiceStub,
            $this->commissionRatioProviderStub,
            $this->baseCurrency
        );
    }

    public function calculateCommissionData()
    {
        return [
            '100 DKK' => [
                new Transaction('54120782', new Money(10000, new Currency('DKK'))),
                new BinInfo('NO', 'ENTERCARD NORGE, A.S.'),
                new Money(1342, new Currency('EUR')),
                0.02,
                new Money(27, new Currency('EUR'))
            ],
            '30 EUR' => [
                new Transaction('47483453', new Money(3000, new Currency('EUR'))),
                new BinInfo('US'),
                null,
                0.02,
                new Money(60, new Currency('EUR'))
            ],
            '50.36 HRK' => [
                new Transaction('54513287', new Money(5036, new Currency('HRK'))),
                new BinInfo('DE', 'EURO KARTENSYSTEME GMBH'),
                new Money(665, new Currency('EUR')),
                0.01,
                new Money(7, new Currency('EUR')),
            ]
        ];
    }

    /**
     * @dataProvider calculateCommissionData
     * @param Transaction $transaction
     * @param BinInfo $binInfo
     * @param Money $exchangedMoney
     * @param float $commissionRate
     * @param Money $expectedCommission
     * @throws \App\Contract\CommissionCalc\CommissionCalcException
     */
    public function testCalculateCommission(
        Transaction $transaction,
        BinInfo $binInfo,
        ?Money $exchangedMoney,
        float $commissionRate,
        Money $expectedCommission
    ): void {

        $this->binInfoProviderStub->method('getBinInfo')
            ->willReturnMap([
                [$transaction->getBin(), $binInfo]
            ]);

        $this->commissionRatioProviderStub->method('getCommissionRatio')
            ->willReturnMap([
                [$binInfo->getCountry(), $commissionRate]
            ]);

        $this->exchangeServiceStub
            ->method('exchangeMoney')
            ->willReturnMap([
                [$transaction->getMoney(), $this->baseCurrency, $exchangedMoney]
            ]);

        $commission = $this->commissionCalc->calculateCommission($transaction);
        $this->assertEquals($expectedCommission, $commission);
    }
}