<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use App\Utility\Chinghwa\Helper\Excel\ExcelHelper;
use Carbon\Carbon;
use Input;
use Maatwebsite\Excel\Facades\Excel;
use Mail;

class PromoGradeController extends Controller
{
    public function index()
    {
        return view('basic.simple', [
            'title' => '銷售模組成效', 
            'des' => '<h4>每月初寄送</h4><pre>' . $this->getQuery() . '</pre>',
            'res' => ''       
        ]);
    }

    public function process()
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        
        $self = $this;

        Excel::create($this->getFileName(), function ($excel) use ($self) {
            ExcelHelper::genBasicSheet(
                $excel, 
                '表格', 
                ['A' => '@','B' => '@', 'L' => '@', 'N' => '@', 'O' => '@'], 
                'Q', 
                $self->getQuery(),
                $self->getExportHead()
            );
        })->store('xls', storage_path('excel/exports'));

        return $msg = $this->send();
    }

    protected function getQuery()
    {
        $dateBegin = '1' == Input::get('week') ? Carbon::now()->modify('first day of this month')->format('Ymd'): Carbon::now()->modify('first day of last month')->format('Ymd'); 

        $dateEnd = '1' == Input::get('week') ? Carbon::now()->modify('last day of this month')->format('Ymd'): Carbon::now()->modify('last day of last month')->format('Ymd');

        return str_replace(
            ['$dateBegin', '$dateEnd'],
            [$dateBegin, $dateEnd],
            file_get_contents(__DIR__ . '/../../../../storage/sql/PromoGrade.sql')
        );
    }

    protected function send()
    {
        $self = $this;

        Mail::send('emails.creditCard', ['title' => $self->getSubject()], function ($m) use ($self) {
            foreach ($self->getToList() as $email => $name) {
                $m->to($email, $name);
            }

            foreach ($self->getCCList() as $email => $name) {
                $m->cc($email, $name);
            }

            $m
                ->subject($self->getSubject())
                ->attach($self->getFilePath());
            ;
        });

        return $this->getSubject() . '發送完畢!';
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

    protected function getFileName()
    {
        return ExportExcel::PROMOGRADE_FILENAME;
    }

    protected function getFilePath()
    {
        return __DIR__ . '/../../../../storage/excel/exports/' . $this->getFileName() .  '.xls';
    }

    protected function getSubject()
    {
        $date = new \DateTime;
        
        if ('1' != Input::get('week')) {
            $date->modify('-1 month');
        }

        return '促銷模組成效' . $date->format('Ym');
    }

    protected function getExportHead()
    {
        return [
            'PromoteSerNo',    
            '訂單單號',    
            '出貨日期',    
            '訂單日期',    
            '商品代號',    
            '商品名稱',    
            '數量',  
            '金額',  
            '金額小計',    
            '部門代號',    
            '部門名稱',    
            '業務代號',    
            '業務姓名',    
            '會員代號',    
            '促銷代號',
            '促銷名稱',    
            '促銷種類'
        ];
    }
}