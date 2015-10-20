<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use Maatwebsite\Excel\Facades\Excel;
use Input;
use Mail;

class EmpPurchaseController extends Controller
{
    public function index()
    {
        return view('basic.simple', [
            'title' => '員工購物銷貨', 
            'des' => '<h4>每周四寄送</h4><pre>' . $this->getQuery() . '</pre>',
            'res' => ''
        ]);
    }

    public function process()
    {
        if (ExportExcel::TOKEN !== Input::get('token')) {
            return 'Unvalid token!';
        }

        if (!$this->hasToSend()) {
            return '員購銷貨單 No Task!';
        }

        $self = $this;

        Excel::create($this->getFileName(), function ($excel) use ($self) {
            // Set the title
            $excel->setTitle('員購銷貨單');

            // Chain the setters
            $excel->setCreator('mis@chinghwa.com.tw')
                    ->setCompany('chinghwa');

            // Call them separately
            $excel->setDescription(ExportExcel::EMPP_FILENAME);

            $self->genBasicSheet($excel, '表格', ['C' => '@','G' => '@','I' => '@'], 'K', $self->getQuery(), $self->getExportHead());
        })->store('xls', storage_path('excel/exports'));

        $msg = $this->send();

    	return $msg;
    }

    /**
     * 判斷本周周二是否為該月最後一個周二
     * 
     * @return boolean
     */
    protected function hasToSend()
    {
        $date = new \DateTime();
        
        $monthCurrent = $date->modify('-2 day')->format('m');
        $monthNext = $date->modify('+6 day')->format('m');

        return $monthCurrent === $monthNext;
    }

    protected function getExportHead()
    {
        return [
            '單據號碼',
            '銷貨日期',
            '業務人員代號',
            '業務人員姓名',
            '部門代號',
            '部門名稱',
            '客戶代號',
            '客戶姓名',
            '商品代號',
            '商品名稱',
            '數量'
        ];
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

    protected function getSubject()
    {
        return '員購銷貨單' . date('Ymd');
    }

    protected function getFileName()
    {
        return ExportExcel::EMPP_FILENAME . '_' . date('Ymd');
    }

    protected function getFilePath()
    {
        return __DIR__ . '/../../../../storage/excel/exports/' . $this->getFileName() .  '.xls';
    }

    protected function getQuery()
    {
        $date = new \DateTime;

        return str_replace(
            ['$dateBegin', '$dateEnd'],
            [$date->modify('first day of this month')->format('Ymd'), $date->modify('last day of this month')->format('Ymd')],
            file_get_contents(__DIR__ . '/../../../../storage/sql/EmpPurchase.sql')
        );
    }

    protected function getToList()
    {
        return [
            'lingying3025@chinghwa.com.tw' => '吳俐穎',            
            'judysu@chinghwa.com.tw' => '蘇怡華'
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
}