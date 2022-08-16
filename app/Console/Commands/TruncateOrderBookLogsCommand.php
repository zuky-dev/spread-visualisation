<?php

namespace App\Console\Commands;

use App\Services\OrderBookLogService;
use Illuminate\Console\Command;

class TruncateOrderBookLogsCommand extends Command
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
    protected $signature = 'orderbook:truncate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncates orderbook for testing purposes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->orderBookLogService->truncateTable();
        $this->info('Orderbook logs table truncated');

        return Command::SUCCESS;
    }
}
