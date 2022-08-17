<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderBookLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'transaction' => $this->transaction,
            'currencyPair' => $this->srcCurrency . ':' . $this->destCurrency,
            'price' => $this->price,
            'quantity' => $this->qty,
            'perUnit' => $this->ppu
        ];
    }
}
