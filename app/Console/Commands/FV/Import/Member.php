<?php

namespace App\Console\Commands\FV\Import;

use App\Export\FV\Import\MemberExport;
use Illuminate\Console\Command;

/**
 * 這邊要特別注意如果使用 utf-8, 預設會自動在檔案表頭加上 BOM, 不然 windows 系統辨識檔案會有問題。
 * 如果不要 BOM 一定要加上參數 --nobom
 */
class Member extends Command
{
    /**
     * The name and signature of the console command.
     * MEMBR 000,000,000,000,000,000,000 => 21碼長度
     *
     * --size: means the chunk size
     * --serno: means the lower(start) serno
     * --upserno: means the end serno
     * --limit: limit of the fetch count of result
     * 
     * @var string
     */
    protected $signature = 'importmember:fv {--size=1500} {--serno=1} {--upserno=999999999999} {--limit=300000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Flap Members';

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

        $export
            ->setCommend($this)
            ->setOutput($this->output)
            ->setChunkSize($this->option('size'))
            ->setCondition(['serno' =>$this->option('serno'), 'upserno' => $this->option('upserno')])
            ->setLimit($this->option('limit'))
            ->handleExport()
        ;
    }
}
