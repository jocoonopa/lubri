<?php

namespace App\Console\Commands\FV\Import;

use Illuminate\Console\Command;

class OrderPipe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * --size: means the chunk size
     * --serno: means the lower(start) serno
     * --upserno: means the end serno
     * --limit: limit of the fetch count of result
     * 
     * @var string
     */
    protected $signature = 'fv:importorderpipe {--serno=1} {--upserno=9999999} {--range=300000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Flap Orders';

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
     * @return mixed
     */
    public function handle()
    {
        set_time_limit(0);

        for ($i = $this->option('serno'); $i < $this->option('upserno'); $i += $this->option('range')) {
            $this->call('fv:importorder', [
                '--serno'   => $i,
                '--upserno' => $i + $this->option('range')
            ]); 
        }
    }
}
