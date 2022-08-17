<?php

namespace App\Repositories;

use App\Models\OrderBookLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class OrderBookLogRepository
{
    protected OrderBookLog $model;

    public function __construct(
        OrderBookLog $model
    )
    {
        $this->model = $model;
    }

    /**
     * Gets either entries from last 10 seconds or all since $since variable
     *
     * @param Carbon|null $since
     * @return Collection
     */
    public function getLatest (Carbon $since = null): Collection
    {
        if (is_null($since)) {
            $since = now()->subSeconds(10);
        }

        return $this->model->where([
            ['created_at', '>', $since]
        ])->get();
    }

    /**
     * Syncs buys and sells entries separately
     * Its easier to distinguish, and simplifies logic
     *
     * @param array $sells
     * @param array $buys
     * @param string $srcCurrency
     * @param string $destCurrency
     * @return void
     */
    public function syncFromData (array $sells, array $buys, string $srcCurrency, string $destCurrency): void
    {
        $this->syncTransaction($sells, 'SELL', $srcCurrency, $destCurrency);
        $this->syncTransaction($buys, 'BUY', $srcCurrency, $destCurrency);
    }

    /**
     * Truncates OrderBookLog model
     *
     * @return void
     */
    public function truncate (): void
    {
        $this->model->truncate();
    }

    /**
     * Adds entries to database
     * Usert alows multi-create with having created_at variable
     * Cex.io provides timestamp, thought its 99% identical to now()
     * TODO: In mission critical usecases tould that be used, now as a prototype its not really necessary
     *
     * @param array $transactions
     * @param string $type
     * @param string $srcCurrency
     * @param string $destCurrency
     * @return void
     */
    private function syncTransaction (array $transactions, string $type, string $srcCurrency, string $destCurrency): void
    {
        $parsedTransactions = $this->parseTransactions($transactions, $type, $srcCurrency, $destCurrency);

        $this->model->upsert($parsedTransactions->toArray(), []);
    }

    /**
     * Filters transactions based on price and value
     * Parses into understandablke for DB array
     * TODO: Under normal circumstance logic would be separated into smaller functions (one for filtering, one for price cut etc.). But due to the smallnes of the method i believe its not necessary for a prototype
     *
     * @param array $transactions
     * @param string $type
     * @param string $srcCurrency
     * @param string $destCurrency
     * @return Collection
     */
    private function parseTransactions (array $transactions, string $type, string $srcCurrency, string $destCurrency): Collection
    {
        $transactions = collect($transactions);

        // Price cutoff
        $smallestSellablePrice = env('CEXIO_CURRENCY_TOO_LOW_CUTOFF', 491.16);
        $transactions = $transactions->filter(function($item) use ($smallestSellablePrice) {
            return $item[0] >= $smallestSellablePrice;
        });

        // Take top values
        if ($type == 'BUY') {
            // I wanna sell for the highest possible price to the bidders (buyers)
            $transactions = $transactions->sortByDesc(function($item) {
                return $item[0] / $item[1];
            });
        }

        if ($type == 'SELL') {
            // I wanna buy for the lowerst possible price from the askers (sellers)
            $transactions = $transactions->sortBy(function($item) {
                return $item[0] / $item[1];
            });
        }

        $countTopValues = env('CEXIO_API_TAKE_BEST', 5);
        $transactions = $transactions->take($countTopValues);

        return $transactions->map(function($item) use ($type, $srcCurrency, $destCurrency) {
            return [
                'src_currency' => $srcCurrency,
                'dest_currency' => $destCurrency,
                'transaction' => $type,
                'price' => $item[0],
                'qty' => $item[1]
            ];
        }, $transactions);
    }

}
