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

    protected $to = [
        'linchengpu@chinghwa.com.tw'    => '5000林振部',
        'fengcheng@chinghwa.com.tw'     => '6600馮誠',
        'swhsu@chinghwa.com.tw'         => '6800徐士偉',
        'sl@chinghwa.com.tw'            => '6700莊淑玲',
        'irenelee.0801@chinghwa.com.tw' => '6100李如玲'
    ];

    protected $cc = [
        'tonyvanhsu@chinghwa.com.tw' => '6820徐士弘',
        'jocoonopa@chinghwa.com.tw'  => '6231小閎'
    ];
    
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

    /**
     * Gets the value of to.
     *
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Sets the value of to.
     *
     * @param mixed $to the to
     *
     * @return self
     */
    protected function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Gets the value of cc.
     *
     * @return mixed
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * Sets the value of cc.
     *
     * @param mixed $cc the cc
     *
     * @return self
     */
    protected function setCc($cc)
    {
        $this->cc = $cc;

        return $this;
    }
}