<?php

use App\Contract\BinInfo\BinInfoProvider;
use App\Contract\CommissionCalc\CommissionCalc as CommissionCalcContract;
use App\Contract\CommissionRatio\CommissionRatioProvider;
use App\Contract\Exchange\ExchangeRatesProvider;
use App\Contract\Exchange\ExchangeService as ExchangeServiceContract;

use App\Contract\Transaction\TransactionsProvider;
use App\Service\BinInfo\BinInfoProviderFromBinlist;
use App\Service\CommissionRatio\CommissionRatioFromConfig;
use App\Service\CommissionCalc\CommissionCalc;
use App\Service\Core;
use App\Service\Exchange\ExchangeRatesProviderFromECB;
use App\Service\Exchange\ExchangeService;
use App\Service\Transaction\TransactionsProviderFromFilesystem;
use App\Service\Utils\Config\Config;
use App\Service\Utils\DI\DIContainer;
use Money\Currencies\ISOCurrencies;
use Money\Parser\DecimalMoneyParser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

return [

    BinInfoProvider::class => function (DIContainer $container): BinInfoProvider {
        $httpClient = $container->make(HttpClientInterface::class);
        return new BinInfoProviderFromBinlist($httpClient);
    },

    ExchangeRatesProvider::class => function (DIContainer $container): ExchangeRatesProvider {
        $httpClient = $container->make(HttpClientInterface::class);
        return new ExchangeRatesProviderFromECB($httpClient);
    },

    ExchangeServiceContract::class => function (DIContainer $container): ExchangeServiceContract {
        $exchangeRatesProvider = $container->make(ExchangeRatesProvider::class);
        return new ExchangeService($exchangeRatesProvider);
    },

    CommissionRatioProvider::class => function (DIContainer $container): CommissionRatioProvider {
        $configService = $container->make(Config::class);
        return new CommissionRatioFromConfig($configService);
    },

    TransactionsProvider::class => function (DIContainer $container, string $fileName): TransactionsProvider {
        return new TransactionsProviderFromFilesystem($fileName, new DecimalMoneyParser(new ISOCurrencies()));
    },

    Core::class => function (DIContainer $container, string $transactionFileName): Core {
        $transactionProvider = $container->make(TransactionsProvider::class, [$transactionFileName]);
        $commissionCalc = $container->make(CommissionCalcContract::class);
        return new Core($transactionProvider, $commissionCalc);
    },

    CommissionCalcContract::class => function (DIContainer $container): CommissionCalcContract {
        $binInfoProvider = $container->make(BinInfoProvider::class);
        $exchangeService = $container->make(ExchangeServiceContract::class);
        $commissionRatioProvider = $container->make(CommissionRatioProvider::class);
        $configService = $container->make(Config::class);

        return new CommissionCalc(
            $binInfoProvider,
            $exchangeService,
            $commissionRatioProvider,
            $configService->getBaseCurrency()
        );
    },

    Config::class => function (): Config {
        $configData = require_once 'config.php';
        return new Config($configData);
    },

    HttpClientInterface::class => function (): HttpClientInterface {
        return HttpClient::create();
    }
];