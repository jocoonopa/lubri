<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use App\Utility\Chinghwa\Helper\Excel\ExcelHelper;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Input;

class BackGoodsController extends Controller
{
	public function index()
    {
        return view('basic.simple', [
            'title' => '每日回貨', 
            'des' => '<h4>每日回貨</h4><pre>' . $this->getQuery() . '</pre>',
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
        return '每日回貨' . date('Ymd');
    }

    protected function getBascitSheetParams()
    {
        return [
            '表格', 
            ['D' => '@', 'I' => '@', 'J' => '@'], 
            'M', 
            $this->getQuery(),
            $this->genHead()
        ];
    }

    protected function getToList()
    {
        return [
            'mis@chinghwa.com.tw' => 'mis'
        ];
    }

    protected function getCCList()
    {
        return [];
    }

    protected function getQuery()
    {
        return str_replace(
            '$date',
            date('Ymd'),
            file_get_contents(__DIR__ . '/../../../../storage/sql/BackGoods.sql')
        );
    }

    protected function getFilePath()
    {
        return __DIR__ . '/../../../../storage/excel/exports/' . $this->getFileName() .  '.' . ExportExcel::XLS;
    }

    protected function genHead()
    {
        return [
            '日期',
            '來回件',
            '景華訂單編號',
            '客戶代號',
            '收貨人姓名',
            '縣市',
            '區',
            '地址',
            '電話1',
            '電話2',
            '收貨時間',
            '備註',
            '備註'
        ];
    }

    protected function getFileName()
    {
        return 'Daily_Back_Goods_' . date('Ymd');
    }
}