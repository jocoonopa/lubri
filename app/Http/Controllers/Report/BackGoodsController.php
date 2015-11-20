<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use App\Utility\Chinghwa\RS\Row;
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
        $subject = '每日回貨' . date('Ymd');
        
        Excel::create($this->getFileName(), function ($excel) use ($self) {
            $self->genBasicSheet(
                $excel, 
                '表格', 
                ['G' => '@','H' => '@'], 
                'J', 
                $self->getQuery(),
                $self->genHead()
            );
        })->store('xlsx', storage_path('excel/exports'));

        $filePath = $this->getFilePath();
            
        Mail::send('emails.creditCard', ['title' => $subject], function ($m) use ($subject, $filePath, $self) {
            $m->subject($subject)->attach($filePath);
            
            foreach ($self->getToList() as $email => $name) {
                $m->to($email, $name);
            }

            foreach ($self->getCCList() as $email => $name) {
                $m->cc($email, $name);
            }
        });

        return $subject . ' send complete!';
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
            'melodyhung@chinghwa.com.tw' => '鑾英',
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
        return __DIR__ . '/../../../../storage/excel/exports/' . $this->getFileName() .  '.xlsx';
    }

    protected function genHead()
    {
        return [
            'OrderNo',
            'Code',
            'ReceiveMan',
            'City',
            'Town',
            'Address',
            'Tel1',
            'Tel2',
            'DeliveryType',
            'Remark'
        ];
    }

    protected function getFileName()
    {
        return 'Daily_Back_Goods_' . date('Ymd');
    }
}