<?php

namespace App\Utility\Chinghwa\Export;

use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Input;

class PromoteShipmentExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
    const DEFAULT_CODE = 'Jocoonopa';
    const DEFAULT_START = '19700101';
    const DEFAULT_END = '21001231';

    public function handle($export)
    {
        set_time_limit(0);

        $promoteQs = json_decode(Input::get('promote-q', ''), true);

        foreach ($promoteQs as $promoteQ) {
            if (!$this->isValidQ($promoteQ)) {
                continue;
            }

            $export->sheet(array_get($promoteQ, 'code'), function ($sheet) use ($promoteQ) {
                $sheet->appendRow($this->getHead());

                foreach ($this->fetch($promoteQ) as $data) {
                    $sheet->appendRow(array_values($data));
                }
            });
        }

        $export->store('xls', storage_path('excel/exports'));

        return $export;
    }

    protected function isValidQ($promoteQ)
    {
        if (empty($promoteQ) || !is_array($promoteQ)) {
            return false;
        }

        return null !== array_get($promoteQ, 'code', null);
    }

    protected function getHead()
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

    protected function fetch($promoteQ)
    {
        $query = $this->getQuery(
            $this->getDateString($promoteQ, 'start_at', self::DEFAULT_START),
            $this->getDateString($promoteQ, 'end_at', self::DEFAULT_END),
            array_get($promoteQ, 'code', self::DEFAULT_CODE)
        );

        return Processor::getArrayResult($query);
    }

    protected function getDateString($promoteQ, $dateString, $default)
    {
        $date = array_get($promoteQ, $dateString);

        return empty(trim($date)) ? self::DEFAULT_START : $date;
    }

    protected function getQuery($start, $end, $code)
    {
        return str_replace(
            ['$startDate', '$endDate', '$promoteCode'], 
            [$start, $end, trim($code)], 
            file_get_contents(__DIR__ . '/../../../../storage/sql/Promote/shipment.sql')
        );
    }
}