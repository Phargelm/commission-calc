<?php

namespace App\Service\Exchange;

use App\Contract\Exchange\ExchangeRatesProvider;
use Money\Currency;
use Money\Exchange;
use Money\Exchange\ReversedCurrenciesExchange;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class ExchangeRatesProviderFromECB
 * @package App\Service\Exchange
 *
 * This class is completely intended to get data from https://api.exchangeratesapi.io.
 * It parses only specific format, returned by this service.
 * If you need to get exchange rates from another source,
 * make a new provider that will implement ExchangeRatesProvider interface.
 */
class ExchangeRatesProviderFromECB implements ExchangeRatesProvider
{
    private const API_URL = 'https://api.exchangeratesapi.io/latest';

    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @inheritDoc
     */
    public function getExchangeRate(Currency $quoteCurrency, Currency $baseCurrency): Exchange
    {
        try {
            $options = ['query' => ['base' => $baseCurrency->getCode()]];
            $response = $this->httpClient->request('GET', static::API_URL, $options);

            if ($response->getStatusCode() !== 200) {
                throw new ExchangeException(sprintf(
                    'Status code %s is received during request to exchange rates service',
                    $response->getStatusCode()
                ));
            }

            $decodedResponse = $response->toArray();

            /**
             * If $decodedResponse['rates'] or $decodedResponse['rates'][$quoteCurrency->getCode()] is not exists,
             * exception should be thrown
             */
            if (!isset($decodedResponse['rates'][$quoteCurrency->getCode()])) {
                throw new ExchangeException('Response from exchange rates service is malformed');
            }

            $rate = $decodedResponse['rates'][$quoteCurrency->getCode()];
            $exchange = new Exchange\FixedExchange([
                $baseCurrency->getCode() => [$quoteCurrency->getCode() => $rate]
            ]);

            return new ReversedCurrenciesExchange($exchange);

        } catch (TransportExceptionInterface $exception) {
            throw new ExchangeException(
                'Error is occurred at the transport level during request to exchange rates service', 0, $exception
            );
        } catch (HttpExceptionInterface $exception) {
            throw new ExchangeException(
                'HTTP-related exception is occured during request to exchange rates service', 0, $exception
            );
        } catch (DecodingExceptionInterface $exception) {
            throw new ExchangeException('Response from exchange rates service is malformed', 0, $exception);
        }

    }
}