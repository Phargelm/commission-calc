<?php

use App\Service\AppException;
use App\Service\Core;
use App\Service\Utils\DI\DIContainer;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;

require_once __DIR__ . '/../vendor/autoload.php';

$debugMode = true;

try {
    $factories = require_once __DIR__ . '/../config/factories.php';

    $di = new DIContainer($factories);
    if (empty($argv[1])) {
        throw new AppException('Please provide filename with input data (e.g. php cli/run.php input.txt)');
    }
    /** @var Core $core */
    $core = $di->make(Core::class, [$argv[1]]);
    $commissions = $core->calculate();
    $formatter = new DecimalMoneyFormatter(new ISOCurrencies());
    foreach ($commissions as $commission) {
        fwrite(STDOUT, $formatter->format($commission) . PHP_EOL);
    }
} catch (\Exception $exception) {
    if ($debugMode) {
        throw $exception;
    }
    fwrite(STDOUT, $exception->getMessage() . PHP_EOL);
}