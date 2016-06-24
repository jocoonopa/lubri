<?php
/*
 * This file is extends of Class Command.
 *
 * (c) Jocoonopa <jocoonopa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Console\Commands\FVSync;

use App\Export\FVSync\MemberExport;
use App\Model\Log\FVSyncQue;
use App\Model\Log\FVSyncType;
use Illuminate\Console\Command;

/**
 * To Sync Flap/Ensound and Viga Member
 * 
 *===============================================================================================
 *                                  Program work flow:
 *================================================================================================
 *
 * 1. Schedule execute syncmember:fv
 * 2. 
 *     Try: 
 *         fetch sync member data "later than last done Que.mdt_time_flag"
 *     Catch:
 *         - New a Que(Assume no error will occur), set Que.status = 1[success], command finish
 * 3. 
 *     if data is NULL: 
 *         ignore, command finish
 *     else:
 *         - New a Que(Assume no error will occur)
 *         - Generate CSV
 *         - Call powershell to move file to 41, and store pwl msg in a variable
 *         - 
 *           if file exist at 41: 
 *               Que.status = 0
 *           else: 
 *               log error in Que.error, set Que.status = 2[cannot generate file @41], command finish
 *               
 * 4. Call powershell to invoke viga proc, to import data.(這裡如果改成能直接在246呼叫偉特程式碼感覺更好些)
 * Try: 
 *     - import, move file from todo folder to done folder.
 * Catch:
 *     - move file from todo to exception
 *
 * 5. Check file locate, 
 * if in done:
 *     Que.status = 1[success], mdt_time_flag = last_member_data.mdt_time
 * elseif in todo:
 *     Que.status = 3[unknown error, might be network or cmd execute, have to digg in]
 * elseif in exception
 *     Que.status = 4[Viga cmd occur some exception]
 *
 * 6. Command finish!!
 */
class Member extends Command
{
    const MAX_LIMIT = 4000;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncmember:fv {--big5} {--size=1500 : means the chunk size}';

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
            ->setChunkSize($this->option('size', self::MAX_LIMIT))
            ->setIsBig5($this->option('big5'))
            ->handleExport()
        ;

        return $this;
    }
}
