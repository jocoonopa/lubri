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
 */
class CallList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importlist:fv {--size=1500} {--limit=300000} {--startat=2016-07-01}';

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
            ->setCondition(['mdtTime' => $this->option('startat')])
            ->handleExport()
        ;
    }
}
