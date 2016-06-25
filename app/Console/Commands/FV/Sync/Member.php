<?php
/*
 * This file is extends of Class Command.
 *
 * (c) Jocoonopa <jocoonopa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Console\Commands\FV\Sync;

use App\Export\FV\Sync\MemberExport;
use App\Model\Log\FVSyncQue;
use App\Model\Log\FVSyncType;
use Illuminate\Console\Command;

/**
 * To Sync Flap/Ensound and Viga Member
 */
class Member extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncmember:fv {--size=1500 : means the chunk size} {--limit=300000}';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(MemberExport $export)
    {
        set_time_limit(0);
        
        $this->proc($export);
    }

    protected function proc(MemberExport $export)
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
