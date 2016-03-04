<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use App\Utility\Chinghwa\Report\DailySaleRecord\NewExcel\DailySaleRecordExport;
use Mail;
use Carbon\Carbon;

class DailySaleRecordController extends Controller
{
    const REPORT_NAME = '每日業績';

    public function index()
    {   
        return view('basic.simple', [
            'title' => self::REPORT_NAME, 
            'des' => NULL,
            'res' => NULL
        ]);
    }

    public function process(DailySaleRecordExport $export)
    {
        $export->handleExport();

        return $this->sendMail($export); 
    }

    protected function sendMail(DailySaleRecordExport $export)
    {
        $subject = $this->getSubject();

        Mail::send('emails.dears', ['title' => $subject], $this->sendCallback($subject, $export));

        return "{$subject} send complete!";
    }

    protected function getSubject()
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

    protected function sendCallback($subject, $export)
    {
        return function ($m) use ($subject, $export) {            
            $filename = $export->getFilename();
            $filePath = __DIR__ . '/../../../../storage/excel/exports/' . $filename .  '.xlsx';

            $m
                ->to('linchengpu@chinghwa.com.tw', '5000林振部')
                ->to('swhsu@chinghwa.com.tw', '6800徐士偉')
                ->to('sl@chinghwa.com.tw', '6700莊淑玲')
                ->cc('tonyvanhsu@chinghwa.com.tw', '6820徐士弘')
                ->cc('jocoonopa@chinghwa.com.tw', '6231小閎')
                ->subject($subject)
                ->attach($filePath);
            ;
        };
    }
}