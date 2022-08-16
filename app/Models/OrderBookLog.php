<?php

namespace App\Models;

use App\Models\BaseModel;

class OrderBookLog extends BaseModel
{
    public function getTotalAttribute(): float
    {
        return round($this->price * $this->qty, 2);
    }
}
