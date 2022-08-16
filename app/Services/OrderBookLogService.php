<?php

namespace App\Services;

use App\Repositories\OrderBookLogRepository;

class OrderBookLogService extends BaseService
{
    public function __construct(
        OrderBookLogRepository $repository
    ){
        $this->repository = $repository;
    }

    public function fetchAndSync(bool $loop = false) {
        // TODO
        if ($loop) {
            // TODO default env time
            // TODO make 3600 as a helper function or constant variable
            $sleepTime = (int) (env('ORDERBOOK_API_LIMIT') / 3600);

            while (true) {
                $data = $this->cexIOFetch();
                $this->repository->syncFromData($data);

                sleep($sleepTime);
            }

        } else {
            $data = $this->cexIOFetch();
            $this->repository->syncFromData($data);
        }
    }

    public function fetchFromApi() {
        // TODO
    }

    private function cexIOFetch(){
        // TODO
        return [];
    }
}
