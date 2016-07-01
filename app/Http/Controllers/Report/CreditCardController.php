<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use App\Utility\Chinghwa\Helper\Excel\ExcelHelper;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Input;

class CreditCardController extends Controller
{
    public function index()
    {
        return view('basic.simple', [
            'title' => '訂單刷卡名單', 
            'des' => '<h4>每日寄送</h4><pre>' . $this->getCreditCardDealQuery() . '</pre>',
            'res' => NULL
        ]);
    }

    /**
     * mailDailyCreditCard 每日訂單成交刷卡名單
     * 
     * @param  Request $request 
     * @return string
     */
    public function mail(Request $request)
    {
        $subject = '訂單成交刷卡名單' . date('Ymd');
        $filename = ExportExcel::DCC_FILENAME . date('Ymd');
        $filePath = __DIR__ . '/../../../../storage/excel/exports/' . $filename .  '.xlsx';

        $excel = $this->genCreditCardDealReport($filename)->store('xlsx', storage_path('excel/exports'));
            
        Mail::send('emails.creditCard', ['title' => $subject], function ($m) use ($subject, $filePath) {
            $m
                ->to('judysu@chinghwa.com.tw', '怡華')
                ->to('migo@chinghwa.com.tw', '惠子')
                ->cc('tonyvanhsu@chinghwa.com.tw', '士弘')
                ->cc('jocoonopa@chinghwa.com.tw', '小閎')
                ->subject($subject)
                ->attach($filePath);
            ;
        });

        return $subject . ' send complete!';
    }

    protected function getCreditCardDealQuery()
    {
        return str_replace(
            '$date',
            date('Ymd'),
            file_get_contents(__DIR__ . '/../../../../storage/sql/CreditCard.sql')
        );
    }

    protected function genCreditCardDealHead()
    {
        return [
            '訂單單號', '單據代號', '單據名稱', '訂單日期', 
            '出貨日期', '應付金額', '訂單金額',
            '會員代號', '會員姓名', '連絡電話', '公司電話', '手機號碼',
            '業務代號', '業務姓名', '部門代號', '部門名稱', '信用卡卡號', 
            '付款代號', '付款方式', '期數', '授權號碼', '刷卡金額'
        ];
    }

    protected function genCreditCardDealReport($filename)
    {
        $self = $this;

        return Excel::create($filename, function($excel) use ($self) {
            $formatArr = [
                'J' => '@',
                'K' => '@',
                'L' => '@',
                'M' => '@',
                'N' => '@',
                'O' => '@',
                'P' => '@',
                'Q' => '@',
                'R' => '@',
                'S' => '@',
                'T' => '@',
                'U' => '@',
                'V' => '0',
                'B' => '@',
                'H' => '@',
                'F' => '0',
                'G' => '0'
            ];

            ExcelHelper::genBasicSheet(
                $excel, 
                '表格', 
                $formatArr, 
                'V', 
                $self->getCreditCardDealQuery(), 
                $self->genCreditCardDealHead()
            );
        });
    }
}