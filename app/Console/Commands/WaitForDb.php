<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class WaitForDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:wait';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wait for the database to come online';

    /**
     * Number of seconds to wait between consecutive attempts to connect to the database.
     *
     * @var int
     */
    protected $interval;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->interval = config('console.db.wait.interval', 5);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $dbIsOnline = false;

        while (!$dbIsOnline) {
            try {
                DB::connection()->getPdo();
                $dbIsOnline = true;
                $this->info("DB connection successful!");
            } catch (\Exception $e) {
                $this->info("DB connection failed, still waiting for DB to come online...");
                sleep($this->interval);
            }
        }
    }
}
