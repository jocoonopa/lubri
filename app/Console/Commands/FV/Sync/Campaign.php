<?php

namespace App\Console\Commands\FV\Sync;

use App\Export\FVSync\MemberExport;
use App\Model\Log\FVSyncLog;
use App\Model\Log\FVSyncType;
use Illuminate\Console\Command;

class Campaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synccampaign:fv {max=500 : The maximum members select once query execute}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Flap Data and trans to Viga';

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
     * @return mixed
     */
    public function handle(MemberExport $export)
    {
    }
}
