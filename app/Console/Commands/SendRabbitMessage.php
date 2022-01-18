<?php

namespace App\Console\Commands;

use App\Jobs\testRabbitJob;
use Illuminate\Console\Command;

class sendRabbitMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send rabbitmq message';

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
        TestRabbitJob::dispatch()->onQueue('application-x');
        return 0;
    }
}
