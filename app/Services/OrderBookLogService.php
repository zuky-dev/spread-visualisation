<?php

namespace App\Services;

use App\Repositories\OrderBookLogRepository;
use App\Traits\LogTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class OrderBookLogService
{
    use LogTrait;

    protected OrderBookLogRepository $repository;
    protected $cexioApi = 'https://cex.io/api/';

    public function __construct(
        OrderBookLogRepository $repository
    ){
        $this->repository = $repository;
    }

    public function fetchAndSync() {
        $data = $this->cexioFetch();

        $this->repository->syncFromData($data->bids, $data->asks, $data->srcCurrency, $data->destCurrency);
    }

    public function latest(string $since = null): Collection {
        return $this->repository->getAll($since);
    }

    public function truncateTable(){
        $this->repository->truncate();
    }

    private function cexioFetch() {
        $currency1 = env('CEXIO_CURRENCY_1', 'ETH');
        $currency2 = env('CEXIO_CURRENCY_2', 'EUR');

        $endpoint = $this->cexioApi . 'order_book/' . $currency1 . '/' . $currency2;

        $client = new Client();

        try {
            $response = $client->request('GET', $endpoint, [
                'query'=> [
                    'depth' => env('CEXIO_API_ORDERBOOK_LIMIT', 100),
                ]
            ]);

            $data = json_decode($response->getBody());
            [$data->srcCurrency, $data->destCurrency] = $this->parseCurrencies($data->pair);

            return $data;
        } catch (ClientException $exception) {
            $this->logger('high', $exception);

            die();
        }
    }

    private function parseCurrencies(string $pair): array
    {
        return explode(':', $pair, 2);
    }
}
