<?php

namespace App\Console\Commands\FV\Sync;

use App\Export\FV\Sync\CampaignExport;
use Illuminate\Console\Command;

class Campaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fv:synccampaign {--size=1500 : means the chunk size} {--limit=300000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Ensound Campaigns with Viga';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(CampaignExport $export)
    {
        set_time_limit(0);
        
        $this->proc($export);
    }

    protected function proc(CampaignExport $export)
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
