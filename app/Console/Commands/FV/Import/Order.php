<?php

namespace App\Console\Commands\FV\Import;

use App\Export\FV\Import\OrderExport;
use Illuminate\Console\Command;

class Order extends Command
{
    const THRESHOLD_CRTTIME = '2014-01-01 00:00:00';

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
    protected $signature = 'fv:importorder {--size=1500} {--serno=1} {--upserno=9999999} {--limit=300000}';

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
    public function handle(OrderExport $export)
    {
        set_time_limit(0);

        $export
            ->setCommend($this)
            ->setOutput($this->output)
            ->setChunkSize($this->option('size'))
            ->setLimit($this->option('limit'))
            ->setCondition(['serno' =>$this->option('serno'), 'upserno' => $this->option('upserno'), 'crttime' => self::THRESHOLD_CRTTIME])
            ->handleExport()
        ;   
    }
}
