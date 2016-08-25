<?php

namespace App\Console\Commands\FV\Sync;

use App\Export\FV\Sync\CalllogExport;
use Illuminate\Console\Command;

class Calllog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fv:synccalllog {--size=1500 : means the chunk size} {--limit=300000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Ensound Calllogs with Viga';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(CalllogExport $export)
    {
        set_time_limit(0);
        
        $this->proc($export);
    }

    protected function proc(CalllogExport $export)
    {
        $export
            ->setCommend($this)
            ->setOutput($this->output)
            ->setChunkSize($this->option('size'))
            ->setLimit($this->option('limit'))
            ->handleExport()
        ;

        return $this;
    }
}
