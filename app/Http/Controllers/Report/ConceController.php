<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use App\Utility\Chinghwa\Helper\Excel\ExcelHelper;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Input;

class ConceController extends Controller
{
    public function index()
    {
        return view('basic.simple', [
            'title' => '康思特', 
            'des' => '<h4>每月初寄送</h4><pre>' . $this->getSaleQuery() . '</pre><hr/><pre>' . $this->getBackQuery() . '</pre>',
            'res' => ''       
        ]);
    }

    public function process()
    {        
        $self = $this;

        Excel::create($this->getFileName(), function ($excel) use ($self) {
            ExcelHelper::genBasicSheet($excel, '銷貨', ['C' => '@','G' => '@', 'I' => '@'], 'K', $self->getSaleQuery(), $self->getExportHead()['sale']);
            ExcelHelper::genBasicSheet($excel, '退貨', ['C' => '@','G' => '@', 'I' => '@'], 'K', $self->getBackQuery(), $self->getExportHead()['back']);
        })->store('xls', storage_path('excel/exports'));

        return $msg = $this->send();
    }

    protected function getSaleQuery()
    {
        $date = new \DateTime;
        $date->modify('-1 month');

        return str_replace(
            '$date',
            $date->format('Ym') . '%',
            file_get_contents(__DIR__ . '/../../../../storage/sql/ConceSale.sql')
        );
    }

    protected function getBackQuery()
    {
        $date = new \DateTime();
        $date->modify('-1 month');

        return str_replace(
            '$date',
            $date->format('Ym') . '%',
            file_get_contents(__DIR__ . '/../../../../storage/sql/ConceBack.sql')
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
            'life@chinghwa.com.tw'   => '6212林春秀'
        ];
    }

    protected function getCCList()
    {
        return [
            'fengcheng@chinghwa.com.tw'  => '6600馮誠',
            'sl@chinghwa.com.tw'         => '6700莊淑玲',
            'swhsu@chinghwa.com.tw'      => '6800徐士偉',
            'tonyvanhsu@chinghwa.com.tw' => '6820徐士弘',
            'jocoonopa@chinghwa.com.tw'  => '6231洪小閎'
        ];
    }

    protected function getFileName()
    {
        $date = new \DateTime;
        $date->modify('-1 month');

        return ExportExcel::CONCE_FILENAME . '_' . $date->format('Ym');
    }

    protected function getFilePath()
    {
        return __DIR__ . '/../../../../storage/excel/exports/' . $this->getFileName() .  '.xls';
    }

    protected function getSubject()
    {
        $date = new \DateTime;
        $date->modify('-1 month');

        return '康思特銷退貨' . $date->format('Ym');
    }

    protected function getExportHead()
    {
        return [
            'sale' => [
                '銷貨單號',
                '銷貨日期',
                '業務人員代號',
                '業務人員姓名',
                '部門代號',
                '部門名稱',
                '廠客代號',
                '廠客姓名',
                '商品代號',
                '商品名稱',
                '數量',
                '稅前定價',
                '定價',
                '單價',
                '稅前金額',
                '稅後金額'
            ],
            'back' => [
                '銷貨單號',
                '銷貨日期',
                '業務人員代號',
                '業務人員姓名',
                '部門代號',
                '部門名稱',
                '廠客代號',
                '廠客姓名',
                '商品代號',
                '商品名稱',
                '數量',
                '稅前定價',
                '定價',
                '單價',
                '稅前金額',
                '稅後金額'
            ]
        ];
    }
}