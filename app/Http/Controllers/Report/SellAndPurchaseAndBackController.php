<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use Maatwebsite\Excel\Facades\Excel;
use App\Utility\Chinghwa\Helper\Excel\ExcelHelper;
use App\Utility\Chinghwa\Helper\Temper;
use Mail;
use Input;

class SellAndPurchaseAndBackController extends Controller
{
    protected $temper;

    public function __construct ()
    {
        $this->temper = new Temper;
        $this->temper->setDate(with(new \DateTime)->modify('-1 month'));
    }

    protected function getDate()
    {
        return $this->temper->getDate();
    }

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
        Excel::create($this->getFileName(), function ($excel) {
            $excel->setTitle('進銷退明細');

            $excel->setCreator('mis@chinghwa.com.tw')
                    ->setCompany('chinghwa');

            $excel->setDescription(ExportExcel::SPB_FILENAME);

            ExcelHelper::genBasicSheet($excel, '退貨原因', array('G' => '@'), 'K', $this->getBackReasonQuery(), $this->getExportHead()['backReason']);
            ExcelHelper::genBasicSheet($excel, '進貨單', array('G' => '@'), 'N', $this->getPurchaseQuery(), $this->getExportHead()['purchase']);
            ExcelHelper::genBasicSheet($excel, '樣品出貨單', array('G' => '@'), 'N', $this->getSampleDispatchQuery(), $this->getExportHead()['sampleDispatch']);
            ExcelHelper::genBasicSheet($excel, '樣品退回單', array('G' => '@'), 'N', $this->getSampleBackQuery(), $this->getExportHead()['sampleBack']);
            ExcelHelper::genBasicSheet($excel, '調撥單據', array('G' => '@'), 'N', $this->getAllocateQuery(), $this->getExportHead()['allocate']);
            ExcelHelper::genBasicSheet($excel, '銷貨退回', array('G' => '@'), 'N', $this->getSoldBackQuery(), $this->getExportHead()['soldBack']);
            ExcelHelper::genBasicSheet($excel, '銷貨單', array('G' => '@'), 'N', $this->getSellQuery(), $this->getExportHead()['sell']);
        })->store('xls', storage_path('excel/exports'));

        $msg = $this->send();

        return $msg;
    }

    protected function getBackReasonQuery()
    {
        return str_replace(
            '$date',
            $this->getDate()->format('Ym') . '%',
            file_get_contents(__DIR__ . '/../../../../storage/sql/BackReason.sql')
        );
    }

    protected function getPurchaseQuery()
    {
        return str_replace(
            '$date',
            $this->getDate()->format('Ym') . '%',
            file_get_contents(__DIR__ . '/../../../../storage/sql/Purchase.sql')
        );
    }

    protected function getSampleDispatchQuery()
    {
        return str_replace(
            '$date',
            $this->getDate()->format('Ym') . '%',
            file_get_contents(__DIR__ . '/../../../../storage/sql/SampleDispatch.sql')
        );
    }

    protected function getSampleBackQuery()
    {
        return str_replace(
            '$date',
            $this->getDate()->format('Ym') . '%',
            file_get_contents(__DIR__ . '/../../../../storage/sql/SampleBack.sql')
        );
    }

    protected function getAllocateQuery()
    {
        return str_replace(
            '$date',
            $this->getDate()->format('Ym') . '%',
            file_get_contents(__DIR__ . '/../../../../storage/sql/Allocate.sql')
        );

        return "";
    }

    protected function getSoldBackQuery()
    {
        return str_replace(
            '$date',
            $this->getDate()->format('Ym') . '%',
            file_get_contents(__DIR__ . '/../../../../storage/sql/SoldBack.sql')
        );
    }

    protected function getSellQuery()
    {
        return str_replace(
            '$date',
            $this->getDate()->format('Ym') . '%',
            file_get_contents(__DIR__ . '/../../../../storage/sql/Sell.sql')
        );
    }

    protected function send()
    {
        Mail::send('emails.creditCard', ['title' => $this->getSubject()], function ($m) {
            foreach ($this->getToList() as $email => $name) {
                $m->to($email, $name);
            }

            foreach ($this->getCCList() as $email => $name) {
                $m->cc($email, $name);
            }

            $m
                ->subject($this->getSubject())
                ->attach($this->getFilePath());
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
        return ExportExcel::SPB_FILENAME . '_' . $this->getDate()->format('Ym');
    }

    protected function getFilePath()
    {
        return __DIR__ . '/../../../../storage/excel/exports/' . $this->getFileName() .  '.xls';
    }

    protected function getSubject()
    {
        return $this->getDate()->format('Ym') . '進銷退明細';
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