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
        return $this->getQuery();
    }

    public function process()
    {
        $self = $this;
        
        Excel::create($this->getFileName(), function ($excel) use ($self) {
            $params = $self->getBascitSheetParams();
            ExcelHelper::genBasicSheet($excel, $params[0], $params[1], $params[2], $params[3], $params[4]);
        })->store(ExportExcel::XLS, storage_path('excel/exports'));  
            
        Mail::send('emails.creditCard', ['title' => $this->getSubject()], function ($m) use ($self) {
            $m->subject($self->getSubject())->attach($self->getFilePath());
            
            $this->addMailGetter($m, $self->getToList())->addMailGetter($m, $self->getCCList(), 'cc');
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

    protected function addMailGetter(&$m, array $list, $action = 'to')
    {
        foreach ($list as $email => $name) {
            $m->$action($email, $name);
        }

        return $this;
    }

    protected function getToList()
    {
        return [
            'oliver@chinghwa.com.tw' => '誌遠',
            'vivian@chinghwa.com.tw' => '玉英'
        ];
    }

    protected function getCCList()
    {
        return [
            'melodyhong@chinghwa.com.tw' => '鑾英',
            'jocoonopa@chinghwa.com.tw' => '小閎'    
        ];
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