<?php

namespace App\Console\Commands\FVImport;

use App\Export\FVImport\MemberExport;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * 這邊要特別注意如果使用 utf-8, 預設會自動在檔案表頭加上 BOM, 不然 windows 系統辨識檔案會有問題。
 * 如果不要 BOM 一定要加上參數 --nobom
 */
class Member extends Command
{
    const SERNO_PREFIX = 'MEMBR';
    const CODE_LENGTH  = 21;
    const START_AT     = '2010-01-01 00:00:00';

    /**
     * The name and signature of the console command.
     * MEMBR 000,000,000,000,000,000,000 => 21碼長度
     *
     * startat: the CRT_TIME must greater than this value
     * endat: default now. the CRT_TIME must lower than this value
     * --size: means the chunk size
     * --serno: means the lower(start) serno
     * --upserno: means the end serno
     * --limit: limit of the fetch count of result
     * --big5: user big5 encoding(ps: default encoding us utf-8&BOM)
     * --nobom: use this option when you want file encoding with utf-8 and without bom
     * 
     * @var string
     */
    protected $signature = 'importmember:fv {startat?} {endat?} {--size=1500} {--serno=1} {--upserno=999999999999} {--limit=300000}{--big5} {--nobom}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        $this->info("\r\n\r\n------------- {$export->getFilename()} MemberExportProcess Begin! -------------\r\n\r\n");

        $export
            ->setCommend($this)
            ->setOutput($this->output)
            ->setSize($this->option('size'))
            ->setSerno($this->genSerNoStr($this->option('serno')))
            ->setUpSerNo($this->genSerNoStr($this->option('upserno')))
            ->setStartAt($this->argument('startat', '2010-01-01 00:00:00'))
            ->setEndAt($this->argument('endat', Carbon::now()->format('Y-m-d H:i:s')))
            ->setIsBig5($this->option('big5'))
            ->setNobom($this->option('nobom'))
            ->setLimit($this->option('limit'))
            ->handleExport()
        ;

        $this->comment("\r\nExport Complete!");
    }

    protected function genSerNoStr($serno)
    {
        return self::SERNO_PREFIX . str_pad($serno, self::CODE_LENGTH, '0', STR_PAD_LEFT);
    }
}
