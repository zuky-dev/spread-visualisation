<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderBookLogIndexRequest;
use App\Http\Resources\OrderBookLogCollection;
use App\Services\OrderBookLogService;
use Illuminate\Http\JsonResponse;

class OrderBookLogController extends Controller
{
    protected OrderBookLogService $service;

    public function __construct(
        OrderBookLogService $service
    ){
        $this->service = $service;
    }

    /**
     * Get latest entries from orderbook and send neatly tidied for API call
     *
     * @param OrderBookLogIndexRequest $request
     * @return JsonResponse
     */
    public function index (OrderBookLogIndexRequest $request): JsonResponse
    {
        $logs = $this->service->latest($request->since);

        return response()->json(new OrderBookLogCollection($logs));
    }
}
