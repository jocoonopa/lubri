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
use App\Model\Log\FVSyncLog;
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
    protected $signature = 'syncmember:fv {max=500 : The maximum members select once query execute}';

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
     * 0. 目前遇到一個問題，不論用 MDT_TIME 或是 LastModifiedDate, 都會遇到一秒有幾十萬筆資料的狀況，
     * 單純用時間條件走會原地打轉，要再想一下應該加入那些條件讓資料迭代正常。
     *
     * Ans: 應該將資料一次匯入的部分切開成另一個 command, 按照 pkey 直接抓
     *
     * 1. 考慮到麻煩的一次性匯入，想設計一個可以不受記憶體限制的chunk機制。
     * 打造ing |=====>------|
     *
     * 2. 不確定偉特那邊吃不吃 utf-8, 若否，要在改變一下程式碼為可設定編碼
     * 3. 246 安裝 PowerShell 
     *
     * @return mixed
     */
    public function handle(MemberExport $export)
    {
        set_time_limit(0);

        $this->comment('Processing, please wait ...');
        
        $this->proc($export);

        $this->comment($export->getFilename());
    }

    protected function proc(MemberExport $export)
    {
        $startTime = microtime(true);

        $max = $this->argument('max') <= self::MAX_LIMIT ? $this->argument('max') : self::MAX_LIMIT; 

        $export->setMax($max)->handleExport();
        
        $cost = microtime(true) - $startTime;

        $this->createLog($export, $cost)->save();

        return $this;
    }

    protected function createLog($export, $cost)
    {
        $log = new FVSyncLog;
        
        $log->exec_cost = $cost;
        $log->type_id   = FVSyncType::where('name', '=', 'member')->first()->id;
        $log->filepath  = $export->getInfo()['path'];
        $log->filename  = $export->getInfo()['file'];
        $log->count     = $export->getCount();
        $log->ip        = env('HOST_IP');
        $log->mrt_time  = $export->getLastMrtTime();

        return $log;
    }
}
