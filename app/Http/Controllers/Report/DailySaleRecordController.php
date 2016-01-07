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
            'title' => self::REPORT_NAME . '[to副總]', 
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
        $subject = self::REPORT_NAME . $export->getDate()->format('Ymd');
        $filename = $export->getFilename();
        $filePath = __DIR__ . '/../../../../storage/excel/exports/' . $filename .  '.xlsx';
        
        Mail::send('emails.dears', ['title' => $subject], function ($m) use ($subject, $filePath) {
            $m
                ->to('sl@chinghwa.com.tw', '6700莊淑玲')
                ->cc('tonyvanhsu@chinghwa.com.tw', '6820徐士弘')
                ->cc('jocoonopa@chinghwa.com.tw', '小閎')
                ->subject($subject)
                ->attach($filePath);
            ;
        });

        return self::REPORT_NAME . ' send complete!';
    }
}