<?php

namespace App\Models;

use App\Models\BaseModel;

class OrderBookLog extends BaseModel
{
    public function getPpuAttribute(): float
    {
        return round($this->price / $this->qty, 2);
    }

    public function getCreatedAtStringAttribute(): string
    {
        return $this->createdAt->format('H:i:s d.m.Y');
    }
}
