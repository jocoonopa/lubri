<?php

namespace App\Console\Commands\FV\Sync;

use App\Export\FV\Sync\ListExport;
use Illuminate\Console\Command;

class CallList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synclist:fv {--size=1500 : means the chunk size} {--limit=300000} {--startat=2016-07-01}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync CTI CampaignCallList with Viga';

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
    public function handle(ListExport $export)
    {
        set_time_limit(0);
        
        $this->proc($export);
    }

    protected function proc(ListExport $export)
    {
        $export
            ->setCommend($this)
            ->setOutput($this->output)
            ->setChunkSize($this->option('size'))
            ->setLimit($this->option('limit'))
            ->setStartDate($this->option('startat'))
            ->handleExport()
        ;

        return $this;
    }
}
