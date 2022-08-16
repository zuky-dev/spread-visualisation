<?php

namespace App\Repositories;

use App\Models\OrderBookLog;

class OrderBookLogRepository extends BaseRepository
{
    public function __construct(OrderBookLog $model)
    {
        $this->model = $model;
    }

    public function syncFromData($data) {
        // TODO
    }
}
