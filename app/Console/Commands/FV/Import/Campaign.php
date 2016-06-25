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

use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * This class is used for the Campaign dump purpose
 */
class Campaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importcampaign:fv {--valid}';

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
    public function handle()
    {
        set_time_limit(0);

        if (!file_exists(storage_path('excel/exports/fvimport/'))) {
            mkdir(storage_path('excel/exports/fvimport/'), 0777, true);
        }

        $fileName = 'import_campaign_' . time() . '.csv';

        $this->info("\r\n\r\n---- {$fileName} CampaignExportProcess Begin! ---\r\n\r\n");        
        $this->proc($fileName);
        $this->comment("\r\nExport Complete!");
    }

    protected function proc($fileName)
    {
        $fname     = storage_path('excel/exports/fvimport/') . "{$fileName}";        
        $file      = fopen($fname, 'w');
        $campaigns = $this->fetchCampaigns();
        $bar       = $this->initAndGetBar($campaigns);
        
        foreach ($campaigns as $campaign) {
            $this->procWrite($campaign, $file);
            
            $bar->advance();
        }

        fclose($file);
    }

    /**
     * 對每個活動物件處理:
     *    1. 略過不需要的欄位
     *    2. 處理字串 
     *        a. 時間格式轉換為 Y/m/d
     *        b. 過濾全形半形空白, 逗號, 換行符號
     * 
     * @param  array  $campaign
     * @param  object $file     [A file system pointer resource that is typically created using fopen().] 
     * @return mixed            [the number of bytes written, or FALSE on error.]
     */
    protected function procWrite($campaign, $file)
    {
        $tmp = [];

        foreach ($campaign as $attr => $val) {      
            if ($this->isIgnoreColumn($attr)) {
                continue;
            }   

            $tmp[] = $this->getFiltedStr($attr, $val);
        }

        return fwrite($file, cb5(implode(',', $tmp)) . "\r\n");
    }

    /**
     * 判斷傳入的屬性(欄位)是否需要忽略
     * 
     * @param  string  $attr 
     * @return boolean      
     */
    protected function isIgnoreColumn($attr)
    {
        return in_array($attr, ['modified_at', 'created_at']);
    }

    /**
     * 取得過濾處理後的字串, 其中針對不同屬性可能有不同對應的特殊處理，
     * 例如需要將日期轉換為 Y/m/d 格式
     * 
     * @param  string $attr [屬性名稱]
     * @param  string $val  [屬性值]
     * @return string       
     */
    protected function getFiltedStr($attr, $val)
    {
        if (in_array($attr, ['StartDate', 'EndDate'])) {
            $val = with(new \DateTime($val))->format('Y/m/d');
        }

        return $this->strProc($val);
    }

    /**
     * 初始化並且取得 ProgressBar 物件
     * 
     * @param  array $campaigns [活動s]
     * @return object \Symfony\Component\Console\Helper\ProgressBar           
     */
    protected function initAndGetBar($campaigns)
    {
        $bar = $this->output->createProgressBar(count($campaigns));
        $bar->setRedrawFrequency(1);
        $bar->setFormat('verbose');
        $bar->setOverwrite(true);

        return $bar;
    }

    /**
     * csv字串處理
     * 
     * @param  string $str
     * @return string      
     */
    protected function strProc($str)
    {
        return csvStrFilter(trim(nfTowf($str)));
    }

    /**
     * fetchCampaigns valid or all
     * 
     * @return array 
     */
    protected function fetchCampaigns()
    {
        $sql = $this->option('valid') 
            ? 'SELECT * FROM Campaign WHERE StartDate <= \'' . Carbon::yesterday()->format('Y-m-d') . '\' AND EndDate >= \'' . Carbon::tomorrow()->format('Y-m-d') . '\'' 
            : 'SELECT * FROM Campaign';

        return Processor::getArrayResult($sql, 'Cti');
    }
}
