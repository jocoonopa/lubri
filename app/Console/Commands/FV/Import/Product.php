<?php

namespace App\Console\Commands\FV\Import;

use App\Export\FV\Import\ProductExport;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Illuminate\Console\Command;

class Product extends Command
{
    /**
     * The name and signature of the console command.
     *
     * --size: means the chunk size
     * --limit: limit of the fetch count of result
     * 
     * @var string
     */
    protected $signature = 'importproduct:fv {--size=1500} {--limit=300000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle(ProductExport $export)
    {
        set_time_limit(0);

        $export
            ->setCommend($this)
            ->setOutput($this->output)
            ->setChunkSize($this->option('size'))
            ->setLimit($this->option('limit'))
            ->setCondition([])
            ->handleExport()
        ;   
    }
}
