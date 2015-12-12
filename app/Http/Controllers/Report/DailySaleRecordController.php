<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use App\Utility\Chinghwa\Report\NewExcel\DailySaleRecordExport;
use Mail;
use Carbon\Carbon;

class DailySaleRecordController extends Controller
{
    public function index()
    {   
        return view('basic.simple', [
            'title' => '每日業績[to副總]', 
            'des' => NULL,
            'res' => NULL
        ]);
    }

    public function process(DailySaleRecordExport $export)
    {
        $export->handleExport();

        $this->sendMail();

        return '每日業績 send complete!';
    }

    protected function sendMail()
    {
        $date = new Carbon;
        $subject = '每日業績' . $date->format('Ymd');
        $filename = ExportExcel::DSR_FILENAME . $date->format('Ymd');
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
    }
}