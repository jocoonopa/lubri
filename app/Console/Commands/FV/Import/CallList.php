<?php
/*
 * This file is extends of Class Command.
 *
 * (c) Jocoonopa <jocoonopa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Console\Commands\FV\Import;

use App\Export\FV\Import\ListExport;
use Illuminate\Console\Command;

/**
 * This class is used for the Campaign dump purpose
 *
 * list: 1000人:4分鐘, 32099 => 50000人: 200分鐘, 約150萬
 */
class CallList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * --inchunk: means target chunk size
     * --size: means the chunk size of entity
     * --serno: means the lower(start) POS_Member.Serno
     * --limit: limit of the fetch count of result
     * --startat: The sync start day we assume
     * 
     * @var string
     */
    protected $signature = 'fv:importlist {--serno=1} {--inchunk=50} {--size=1500} {--limit=300000} {--startat=2016-07-01}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Ensound CampaignCallList';

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

        $export
            ->setCommend($this)
            ->setOutput($this->output)
            ->setChunkSize($this->option('size'))
            ->setLimit($this->option('limit'))
            ->setCondition(['mdtTime' => "{$this->option('startat')} 00:00:00", 'inchunk' => $this->option('inchunk')])
            ->handleExport()
        ;
    }
}
