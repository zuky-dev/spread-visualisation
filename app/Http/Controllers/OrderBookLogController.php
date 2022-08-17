<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderBookLogIndexRequest;
use App\Http\Resources\OrderBookLogCollection;
use App\Http\Resources\OrderBookLogResource;
use App\Services\OrderBookLogService;

class OrderBookLogController extends Controller
{
    protected OrderBookLogService $service;

    public function __construct(
        OrderBookLogService $service
    ){
        $this->service = $service;
    }

    public function index(OrderBookLogIndexRequest $request){
        $logs = $this->service->latest($request->since);

        return response()->json(new OrderBookLogCollection($logs));
    }
}
