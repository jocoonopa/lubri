<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Input;

class CreditCardUpBrushController extends Controller
{
    const HRS_EMP_CODE = '20141205';

    public function index()
    {
        return view('basic.simple', [
            'title' => '補刷單刷卡名單', 
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
    public function process(Request $request)
    {
        $subject = '補刷單刷卡名單' . date('Ymd');
        $filename = ExportExcel::DCCU_FILENAME . date('Ymd');
        $filePath = __DIR__ . '/../../../../storage/excel/exports/' . $filename .  '.xlsx';

        $excel = $this->genCreditCardDealReport($filename)->store('xlsx', storage_path('excel/exports'));
            
        Mail::send('emails.creditCard', ['title' => $subject], function ($m) use ($subject, $filePath) {
            $m
                // ->to('judysu@chinghwa.com.tw', '怡華')
                // ->cc('tonyvanhsu@chinghwa.com.tw', '士弘')
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
            ['$date', '$code'],
            [date('Ymd'), self::HRS_EMP_CODE],
            file_get_contents(__DIR__ . '/../../../../storage/sql/CreditCardBrushUp.sql')
        );
    }

    protected function genCreditCardDealHead()
    {
        return array(
            '訂單單號', '單據代號', '單據名稱', '更改日期', 
            '出貨日期', '應付金額', '訂單金額',
            '會員代號', '會員姓名', '連絡電話', '公司電話', '手機號碼',
            '業務代號', '業務姓名', '部門代號', '部門名稱', '信用卡卡號', 
            '付款代號', '付款方式', '期數', '授權號碼', '刷卡金額'
        );
    }

    protected function genCreditCardDealReport($filename)
    {
        $self = $this;

        return Excel::create($filename, function($excel) use ($self) {
            $formatArr = array(
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
            );

            $self->genBasicSheet(
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