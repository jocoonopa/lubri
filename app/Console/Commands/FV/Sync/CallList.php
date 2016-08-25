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
    protected $signature = 'fv:synclist {--size=1500 : means the chunk size} {--limit=300000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Ensound CampaignCallList with Viga';
    
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
            ->handleExport()
        ;

        if (NULL !== $export->getQueId()) {
            $this->call('fv:listrep', [
                '--id' => $export->getQueId()
            ]);
        }
        
        return $this;
    }
}
