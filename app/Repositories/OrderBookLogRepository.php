<?php

namespace App\Repositories;

use App\Models\OrderBookLog;
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

    public function getAll(string $since = null): Collection
    {
        if(is_null($since)) {
            $since = now()->subSeconds(10);
        }

        return $this->model->where([
            ['created_at', '>=', $since]
        ])->get();
    }

    public function syncFromData(array $sells, array $buys, string $srcCurrency, string $destCurrency) {
        $this->syncTransaction($sells, 'SELL', $srcCurrency, $destCurrency);
        $this->syncTransaction($buys, 'BUY', $srcCurrency, $destCurrency);
    }

    public function truncate() {
        $this->model->truncate();
    }

    private function syncTransaction(array $transactions, string $type, string $srcCurrency, string $destCurrency)
    {
        $parsedTransactions = $this->parseTransactions($transactions, $type, $srcCurrency, $destCurrency);

        $this->model->upsert($parsedTransactions->toArray(), []);
    }

    private function parseTransactions(array $transactions, string $type, string $srcCurrency, string $destCurrency): Collection
    {
        $transactions = collect($transactions);

        // price cutoff
        $smallestSellablePrice = env('CEXIO_CURRENCY_2_LOW_CUTOFF', 491.16);
        $transactions = $transactions->filter(function($item) use ($smallestSellablePrice) {
            return $item[0] * $item[1] >= $smallestSellablePrice;
        });

        //take top values
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
