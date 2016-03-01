<?php

namespace App\Utility\Chinghwa\Export;

use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Carbon\Carbon;
use Input;

class SaleRecordExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
    public function handle($export)
    {
        return $export->sheet('salerecord', function ($sheet) {
            $sheet->appendRow(array_merge(['部門', '姓名'], $this->getMonthColumns()));

            foreach ($this->shapePersonRows() as $personName => $personRow) {
                $sheet->appendRow(array_merge([$personRow['corp'], $personName], $this->getMonthRecord($personRow)));
            }
        });
    }

    protected function fetchEachMonthData($dateString)
    {
        $data = [];

        for ($i = 0; $i < 12; $i ++) {
            $date = with(new Carbon($dateString))->modify("+ {$i} months");

            $data[] = Processor::getArrayResult($this->getFetchQuery(
                $date->modify('first day of this month')->format('Ymd'), 
                $date->modify('last day of this month')->format('Ymd')
            ));
        }

        return $data;
    }
    
    protected function shapePersonRows()
    {
        $personRows = [];

        foreach ($this->fetchEachMonthData(Input::get('start', '2015-01-01')) as $key => $monthData) {
            $this->extendPersonRows($personRows, $monthData);
        }

        return $personRows;
    }

    protected function extendPersonRows(array &$personRows, array $monthData)
    {
        foreach ($monthData as $eachPerson) {
            if (false === strpos($eachPerson['部門'], '客戶經營')) {
                continue;
            }

            if (!array_key_exists($eachPerson['姓名'], $personRows)) {
                $personRows[$eachPerson['姓名']] = [];

                $personRows[$eachPerson['姓名']]['corp'] = $eachPerson['部門'];
            }

            $personRows[$eachPerson['姓名']][$eachPerson['月份']] = $eachPerson['淨額'];
        }

        return $this;
    }

    public function getMonthColumns()
    {
        $arr = [];

        for ($i = 1; $i <= 12; $i ++) {
            $arr[] = ($i < 10) ?  "20150{$i}" : "2015{$i}";
        }

        return $arr;
    }

    public function getMonthRecord(array $per)
    {
        $record = [];

        for ($i = 1; $i <= 12; $i ++) {
            $record[] = array_get($per, ($i < 10) ?  "20150{$i}" : "2015{$i}", 0);
        }

        return $record;
    }

    protected function getFetchQuery($startDate, $endDate)
    {
        return str_replace(['$startDate', '$endDate'], [$startDate, $endDate], file_get_contents(__DIR__ . '/../../../../storage/sql/DailySaleRecord/ERP.sql'));
    }
}