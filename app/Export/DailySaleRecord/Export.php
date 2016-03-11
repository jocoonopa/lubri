<?php

namespace App\Export\DailySaleRecord;

use App\Utility\Chinghwa\ExportExcel;
use Carbon\Carbon;

class Export extends \Maatwebsite\Excel\Files\NewExcelFile 
{
    const REPORT_NAME         = '每日業績';
    const CTI_JOIN_COLUMN     = '人員代碼';
    const ERP_CORPCODE_COLUMN = '部門代碼';
    const POS_CORPCODE_COLUMN = '門市代號';
    const POS_NONEXIST_GROUP  = '未知門市';
    const ERP_OUTTUNNEL       = 'outTunnel';
    
    protected $date;

    public function getFilename()
    {
        $this->date = Carbon::now()->modify('-1 Days');

        return ExportExcel::DSR_FILENAME . $this->date->format('Ymd');
    }

    public function getRealpath()
    {
        return __DIR__ . "/../../../storage/excel/exports/{$this->getFilename()}.xlsx";
    }

    public function getSubject()
    {
        $carbon = Carbon::now()->modify('-1 days');

        $map = [
            '01' => '一月份',
            '02' => '二月份',
            '03' => '三月份',
            '04' => '四月份',
            '05' => '五月份',
            '06' => '六月份',
            '07' => '七月份',
            '08' => '八月份',
            '09' => '九月份',
            '10' => '十月份',
            '11' => '十一月份',
            '12' => '十二月份'
        ];

        return array_get($map, $carbon->format('m')) . self::REPORT_NAME . $carbon->format('md');
    }

    public function getDate()
    {
        return $this->date;
    }
}