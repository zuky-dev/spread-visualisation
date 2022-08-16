<?php

namespace App\Console\Commands;

use App\Services\OrderBookLogService;
use Illuminate\Console\Command;

class OrderBookCommand extends Command
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
        $this->orderBookLogService->fetchAndSync($this->option('loop'));

        return Command::SUCCESS;
    }
}
