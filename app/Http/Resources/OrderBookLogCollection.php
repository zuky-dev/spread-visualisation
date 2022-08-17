<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderBookLogCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $group = $this->groupBy('createdAtString');

        $arr = [];

        foreach ($group as $date => $transactions) {
            $transactions = $transactions->groupBy('transaction');

            $arr[$date] = [];

            foreach ($transactions as $type => $logs) {
                $arr[$date][$type] = OrderBookLogResource::collection($logs);
            }
        }

        return $arr;
    }
}
