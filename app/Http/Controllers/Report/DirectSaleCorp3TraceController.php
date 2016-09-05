<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use App\Utility\Chinghwa\Helper\Excel\ExcelHelper;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Input;

class DirectSaleCorp3TraceController extends Controller
{
	public function index()
    {
        return view('basic.simple', [
            'title' => '客經三成效追蹤', 
            'des' => '<h4>客經三成效追蹤</h4><pre>' . $this->getQuery() . '</pre>',
            'res' => ''       
        ]);
    }

    public function process()
    { 
        Excel::create($this->getFileName(), function ($excel) {
            $params = $this->getBascitSheetParams();
            ExcelHelper::genBasicSheet($excel, $params[0], $params[1], $params[2], $params[3], $params[4]);
        })->store(ExportExcel::XLS, storage_path('excel/exports'));  
            
        Mail::send('emails.creditCard', ['title' => $this->getSubject()], function ($m) {
            $m->subject($this->getSubject())->attach($this->getFilePath());
            $m->to($this->getToList())->cc($this->getCCList());
        });

        return "{$this->getSubject()} send complete!";
    }

    protected function getSubject()
    {
        return '客三名單成效_' . date('Ymd');
    }

    protected function getBascitSheetParams()
    {
        return [
            '表格', 
            [], 
            'F', 
            $this->getQuery(),
            $this->genHead()
        ];
    }

    protected function getToList()
    {
        return [
            'fengcheng@chinghwa.com.tw' => '6600馮誠',
            'sl@chinghwa.com.tw'        => '6700莊淑玲',
            'swhsu@chinghwa.com.tw'     => '6800徐士偉',
            'sharon@chinghwa.com.tw'    => '6110張佳園',
            'lynn-s1189@chinghwa.com.tw' => '6151許逸玲'
        ];
    }

    protected function getCCList()
    {
        return [
            'tonyvanhsu@chinghwa.com.tw' => '6820徐士弘'
        ];
    }

    protected function getQuery()
    {
        return str_replace(
            '$date',
            date('Ymd'),
            file_get_contents(__DIR__ . '/../../../../storage/sql/DirectSaleCorp3Trace.sql')
        );
    }

    protected function getFilePath()
    {
        return __DIR__ . '/../../../../storage/excel/exports/' . $this->getFileName() .  '.' . ExportExcel::XLS;
    }

    protected function genHead()
    {
        return [
            'Today',
            'MaxDate',
            'MemCount',
            'MemBase',
            'Rate',
            'NetTtl'
        ];
    }

    protected function getFileName()
    {
        return 'DirectSaleCorp3Trace' . date('Ymd');
    }
}