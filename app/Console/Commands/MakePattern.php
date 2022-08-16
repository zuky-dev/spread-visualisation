<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakePattern extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:pattern {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Controller-Service-Repository-Model instance';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('make:model', ['name' => $this->getNameInput()]);
        $this->call('make:repository', ['name' => $this->getNameInput() . 'Repository']);
        $this->call('make:service', ['name' => $this->getNameInput() . 'Service']);
        $this->call('make:controller-service', ['name' => $this->getNameInput() . 'Controller']);
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return trim($this->argument('name'));
    }
}
