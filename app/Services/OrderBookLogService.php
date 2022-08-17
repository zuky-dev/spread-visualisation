<?php

namespace App\Services;

use App\Repositories\OrderBookLogRepository;
use App\Traits\LogTrait;
use Carbon\Carbon;
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

    /**
     * Fetch Cex.io api and save data
     *
     * @return void
     */
    public function fetchAndSync (): void
    {
        $data = $this->cexioFetch();

        $this->repository->syncFromData($data->bids, $data->asks, $data->srcCurrency, $data->destCurrency);
    }

    /**
     * Returns latest entries
     *
     * @param string|null $since
     * @return Collection
     */
    public function latest (string $since = null): Collection
    {
        return $this->repository->getLatest(is_null($since) ? null : Carbon::parse($since));
    }

    /**
     * Truncates repository model
     *
     * @return void
     */
    public function truncateTable (): void
    {
        $this->repository->truncate();
    }

    /**
     * Tries to fetch data from Cex.io
     *
     * @return object
     */
    private function cexioFetch (): object
    {
        $currency1 = env('VITE_CEXIO_CURRENCY_1', 'ETH');
        $currency2 = env('VITE_CEXIO_CURRENCY_2', 'EUR');

        $endpoint = $this->cexioApi . 'order_book/' . $currency1 . '/' . $currency2;

        $client = new Client();

        try {
            $response = $client->request('GET', $endpoint, [
                'query'=> [
                    'depth' => env('CEXIO_API_ORDERBOOK_LIMIT', 100),
                ]
            ]);


            $data = json_decode($response->getBody());

            if (isset($data->error)) {
                echo $data->error;
                die();
            }

            [$data->srcCurrency, $data->destCurrency] = $this->parseCurrencies($data->pair);

            return $data;
        } catch (ClientException $exception) {

            /**
             * if local enviroment, just DDs exception
             * on production saves exception to db for easier develop view
             */
            $this->logger('high', $exception);

            die();
        }
    }

    /**
     * Parse pair into separate currencies
     *
     * @param string $pair
     * @return array
     */
    private function parseCurrencies (string $pair): array
    {
        return explode(':', $pair, 2);
    }
}
