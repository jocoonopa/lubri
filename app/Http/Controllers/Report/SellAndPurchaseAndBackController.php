<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Input;

class SellAndPurchaseAndBackController extends Controller
{
    public function index()
    {
        $queryString = '<h4>退貨原因</h4><pre>' . $this->getBackReasonQuery() . '</pre>';
        $queryString.= '<h4>進貨單</h4><pre>' . $this->getPurchaseQuery() . '</pre>';
        $queryString.= '<h4>樣品出貨單</h4><pre>' . $this->getSampleDispatchQuery() . '</pre>';
        $queryString.= '<h4>樣品退回單</h4><pre>' . $this->getSampleBackQuery() . '</pre>';
        $queryString.= '<h4>調撥單據</h4><pre>' . $this->getAllocateQuery() . '</pre>';
        $queryString.= '<h4>銷貨退回</h4><pre>' . $this->getSoldBackQuery() . '</pre>';
        $queryString.= '<h4>銷貨單</h4><pre>' . $this->getSellQuery() . '</pre>';  

        return view('basic.simple', [
            'title' => '進銷退(每月初寄送)', 
            'des' => $queryString,
            'res' => NULL        
        ]);
    }

    public function process()
    {
        if (ExportExcel::TOKEN !== Input::get('token')) {
            return 'Unvalid token!';
        }

        $self = $this;

        Excel::create($this->getFileName(), function ($excel) use ($self) {
            // Set the title
            $excel->setTitle('進銷退明細');

            // Chain the setters
            $excel->setCreator('mis@chinghwa.com.tw')
                    ->setCompany('chinghwa');

            // Call them separately
            $excel->setDescription(ExportExcel::SPB_FILENAME);

            $self
                ->genBasicSheet($excel, '退貨原因', array('G' => '@'), 'K', $self->getBackReasonQuery(), $self->getExportHead()['backReason'])
                ->genBasicSheet($excel, '進貨單', array('G' => '@'), 'N', $self->getPurchaseQuery(), $self->getExportHead()['purchase'])
                ->genBasicSheet($excel, '樣品出貨單', array('G' => '@'), 'N', $self->getSampleDispatchQuery(), $self->getExportHead()['sampleDispatch'])
                ->genBasicSheet($excel, '樣品退回單', array('G' => '@'), 'N', $self->getSampleBackQuery(), $self->getExportHead()['sampleBack'])
                ->genBasicSheet($excel, '調撥單據', array('G' => '@'), 'N', $self->getAllocateQuery(), $self->getExportHead()['allocate'])
                ->genBasicSheet($excel, '銷貨退回', array('G' => '@'), 'N', $self->getSoldBackQuery(), $self->getExportHead()['soldBack'])
                ->genBasicSheet($excel, '銷貨單', array('G' => '@'), 'N', $self->getSellQuery(), $self->getExportHead()['sell'])
            ;
        })->store('xls', storage_path('excel/exports'));

        $msg = $this->send();

        return $msg;
    }

    protected function getBackReasonQuery()
    {
        $date = new \DateTime;
        $date->modify('-1 month');
        $date = $date->format('Ym') . '%';

        return str_replace(
            '$date',
            $date,
            file_get_contents(__DIR__ . '/../../../../storage/sql/BackReason.sql')
        );
    }

    protected function getPurchaseQuery()
    {
        $date = new \DateTime;
        $date->modify('-1 month');
        $date = $date->format('Ym') . '%';

        return str_replace(
            '$date',
            $date,
            file_get_contents(__DIR__ . '/../../../../storage/sql/Purchase.sql')
        );
    }

    protected function getSampleDispatchQuery()
    {
        $date = new \DateTime;
        $date->modify('-1 month');
        $date = $date->format('Ym') . '%';

        return str_replace(
            '$date',
            $date,
            file_get_contents(__DIR__ . '/../../../../storage/sql/SampleDispatch.sql')
        );
    }

    protected function getSampleBackQuery()
    {
        $date = new \DateTime;
        $date->modify('-1 month');
        $date = $date->format('Ym') . '%';

        return str_replace(
            '$date',
            $date,
            file_get_contents(__DIR__ . '/../../../../storage/sql/SampleBack.sql')
        );
    }

    protected function getAllocateQuery()
    {
        $date = new \DateTime;
        $date->modify('-1 month');
        $date = $date->format('Ym') . '%';

        return str_replace(
            '$date',
            $date,
            file_get_contents(__DIR__ . '/../../../../storage/sql/Allocate.sql')
        );

        return "";
    }

    protected function getSoldBackQuery()
    {
        $date = new \DateTime;
        $date->modify('-1 month');
        $date = $date->format('Ym') . '%';

        return str_replace(
            '$date',
            $date,
            file_get_contents(__DIR__ . '/../../../../storage/sql/SoldBack.sql')
        );
    }

    protected function getSellQuery()
    {
        $date = new \DateTime;
        $date->modify('-1 month');
        $date = $date->format('Ym') . '%';

        return str_replace(
            '$date',
            $date,
            file_get_contents(__DIR__ . '/../../../../storage/sql/Sell.sql')
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
            'vivian@chinghwa.com.tw' => '謝玉英'
        ];
    }

    protected function getCCList()
    {
        return [
            'sl@chinghwa.com.tw' => '莊淑玲',
            'tonyvanhsu@chinghwa.com.tw' => '徐士弘',
            'jocoonopa@chinghwa.com.tw' => '洪小閎'
        ];
    }

    protected function getFileName()
    {
        $date = new \DateTime;
        $date->modify('-1 month');

        return ExportExcel::SPB_FILENAME . '_' . $date->format('Ym');
    }

    protected function getFilePath()
    {
        return __DIR__ . '/../../../../storage/excel/exports/' . $this->getFileName() .  '.xls';
    }

    protected function getSubject()
    {
        $date = new \DateTime;
        $date->modify('-1 month');

        return $date->format('Ym') . '進銷退明細';
    }

    protected function getExportHead()
    {
        return [
            'backReason' => [
                '退貨單據代號', 
                '退貨日期', 
                '原訂單單號', 
                '來源單號', 
                '部門代號', 
                '部門名稱', 
                '業務代號', 
                '業務姓名', 
                '退貨原因代號', 
                '退貨原因', 
                '備註'
            ],  

            'purchase' => [
                '進貨日期', 
                '進貨單號', 
                '單據代號', 
                '單據名稱', 
                '部門代號', 
                '部門名稱', 
                '進貨人員代號', 
                '進貨人員姓名', 
                '未稅金額', 
                '稅額', 
                '含稅金額', 
                '倉別代號',
                '倉別名稱',
                '數量'
            ],
                                                        
            'sampleDispatch' => [
                '樣品出貨日期', 
                '樣品出貨單號', 
                '單據代號', 
                '單據名稱', 
                '部門代號', 
                '部門名稱', 
                '樣品出貨人員代號', 
                '樣品出貨姓名', 
                '未稅金額', 
                '稅額', 
                '含稅金額', 
                '倉別代號', 
                '倉別名稱', 
                '數量'
            ],
                                                        
            'sampleBack' => [
                '樣品退貨日期', 
                '樣品退貨單號', 
                '單據代號', 
                '單據名稱', 
                '部門代號', 
                '部門名稱', 
                '樣品退貨人員代號', 
                '樣品退貨姓名', 
                '未稅金額', 
                '稅額', 
                '含稅金額', 
                '倉別代號', 
                '倉別名稱', 
                '數量'
            ],       

            'allocate' => [
                '調撥日期', 
                '調撥單號', 
                '單據代號', 
                '單據名稱', 
                '部門代號', 
                '部門名稱', 
                '調撥人代號', 
                '調撥人姓名', 
                '調撥入倉代號', 
                '調撥入倉名稱', 
                '調撥出倉代號', 
                '調撥出倉名稱', 
                '數量',
                '金額'
            ],
                                                          
            'soldBack' => [
                '銷貨退回日期', 
                '銷貨退回單號', 
                '單據代號', 
                '單據名稱', 
                '部門代號', 
                '部門名稱', 
                '銷貨退回人員代號', 
                '銷貨退回人員姓名', 
                '未稅金額', 
                '稅額', 
                '含稅金額', 
                '倉別代號',
                '倉別名稱',
                '數量'
            ],
                                                          
            'sell' => [
                '銷貨日期', 
                '銷貨單號', 
                '單據代號', 
                '單據名稱', 
                '部門代號', 
                '部門名稱', 
                '銷貨人員代號', 
                '銷貨人員姓名', 
                '未稅金額', 
                '稅額', 
                '含稅金額', 
                '倉別代號', 
                '倉別名稱', 
                '數量'
            ]
        ];
    }
}