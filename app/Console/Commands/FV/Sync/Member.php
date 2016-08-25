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
    protected $signature = 'fv:syncmember {--size=1500 : means the chunk size} {--limit=300000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Flap Members with Viga';

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
