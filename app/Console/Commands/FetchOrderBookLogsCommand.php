<?php

namespace App\Console\Commands;

use App\Services\OrderBookLogService;
use Illuminate\Console\Command;

class FetchOrderBookLogsCommand extends Command
{
    protected OrderBookLogService $orderBookLogService;

    public function __construct(
        OrderBookLogService $orderBookLogService
    )
    {
        parent::__construct();

        $this->orderBookLogService = $orderBookLogService;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orderbook:fetch {--loop}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the top of the order book';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $loop = $this->option('loop');

        if ($loop) {
            $sleepTime = (int) (dayInSeconds() / env('CEXIO_API_LIMIT', (dayInSeconds() / 2)));

            while (true) {
                $this->fetch();

                //TODO: this sleep should be changed to something less server taxing
                sleep($sleepTime);
            }

        } else {
            $this->fetch();
        }

        return Command::SUCCESS;
    }

    private function fetch(){
        $this->orderBookLogService->fetchAndSync();

        $this->info('Fetched orderbook from Cex.io (' . env('CEXIO_CURRENCY_1', 'ETH') . ':' . env('CEXIO_CURRENCY_2', 'EUR') . ') at ' . now()-> format('H:i:s d.m.Y'));
    }
}
