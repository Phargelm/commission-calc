<?php

namespace App\Service\BinInfo;

use App\Contract\BinInfo\BinInfo as BinInfoContract;
use App\Contract\BinInfo\BinInfoProvider;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BinInfoProviderFromBinlist implements BinInfoProvider
{
    private const API_URL = 'https://lookup.binlist.net/%s';

    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @inheritDoc
     */
    public function getBinInfo(string $bin): BinInfoContract
    {
        try {
            $response = $this->httpClient->request('GET', sprintf(static::API_URL, $bin));

            if ($response->getStatusCode() !== 200) {
                throw new BinInfoException(sprintf(
                    'Status code %s is received during request to bin info service',
                    $response->getStatusCode()
                ));
            }

            $decodedResponse = $response->toArray();

            if (!isset($decodedResponse['country']['alpha2'])) {
                throw new BinInfoException('Response from bin info service is malformed', 0);
            }

            return new BinInfo(
                $decodedResponse['country']['alpha2'],
                $decodedResponse['bank']['name'] ?? null,
                $decodedResponse['prepaid'] ?? null
            );

        } catch (TransportExceptionInterface $exception) {
            throw new BinInfoException(
                'Error is occurred at the transport level during request to bin info service', 0, $exception
            );
        } catch (HttpExceptionInterface $exception) {
            throw new BinInfoException(
                'HTTP-related exception is occured during request to bin info service', 0, $exception
            );
        } catch (DecodingExceptionInterface $exception) {
            throw new BinInfoException('Response from bin info service is malformed', 0, $exception);
        }
    }

}