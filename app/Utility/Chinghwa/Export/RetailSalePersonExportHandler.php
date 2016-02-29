<?php

namespace App\Utility\Chinghwa\Export;

use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Carbon\Carbon;

class RetailSalePersonExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
    const AREA = '分區';
    const STORE = '門市';

    protected $thisMonthEmpRes = [];
    protected $thisYearEmpRes = [];
    protected $thisMonthASFormatArr = [];
    protected $thisYearASFormatArr = [];
    
    private $rows = [];
    private $index = 2;

    private $indexOfStore = [];
    private $indexOfArea = [];

    private $storeCssRange = [];
    private $areaCssRange = [];
    private $totalCssRange = [];

    public function handle($export)
    {
        $this
            ->setThisMonthEmpRes($this->getThisMonthRetailSaleRes())
            ->setThisYearEmpRes($this->getThisYearRetailSaleRes())
            ->setThisMonthASFormatArr($this->genThisMonthASFormatArr())
            ->setThisYearASFormatArr($this->genThisYearASFormatArr())
            ->drawThisMonth()
            ->drawThisYear()
        ;

        $export->sheet($export->getFilename(), $this->getExportCallback())->store('xls', storage_path('excel/exports'));

        return $export;
    }

    protected function getExportCallback()
    {
        return function($sheet) {
            $sheet
                ->setWidth($this->getWidth())
                ->setBorder('A1:F1', 'thin')
                ->setBorder('H1:M1', 'thin')
                ->setColumnFormat($this->getColumnFormat())
                ->appendRow(1, $this->getTopColumnName())            
                ->rows($this->rows)
                ->cells('A1:M1', function ($cells) {
                    $cells->setFont([
                        'size' => '12',
                        'bold' =>  true
                    ]);
                })
            ;

            $this->applyCssStyle($sheet);
        };
    }

    protected function applyCssStyle($sheet)
    {
        return $this->setSheetStoreCss($sheet)->setSheetAreaCss($sheet)->setSheetTotalCss($sheet);
    }

    protected function setSheetStoreCss($sheet)
    {
        foreach ($this->storeCssRange as $range) {
            $sheet->cells($range, function($cells) {
                $cells->setBackground('F5E3DA')->setFontColor('#000000')->setFontWeight('bold');
            });
        }

        return $this;
    }

    protected function setSheetAreaCss($sheet)
    {
        foreach ($this->areaCssRange as $range) {
            $sheet->cells($range, function($cells) {
                $cells->setBackground('EDCCBB')->setFontColor('#000000')->setFontWeight('bold');
            });
        }

        return $this;
    }

    protected function setSheetTotalCss($sheet)
    {
        foreach ($this->totalCssRange as $range) {
            $sheet->cells($range, function($cells) {
                $cells->setBackground('E8986F')->setFontColor('#000000')->setFontWeight('bold');
            });
        }

        return $this;
    }

    protected function drawThisMonth()
    {
        foreach ($this->getThisMonthASFormatArr() as $areaName => $areaRes) {
            $this->drawMonthAreaRow($areaName, $areaRes);
        }

        $this->rows[$this->index] = [
            '合計', 
            $this->getSumFormula($this->getIndexOfArea(), 'B'),
            "=B{$this->index}/F{$this->index}",
            $this->getSumFormula($this->getIndexOfArea(), 'D'),
            "=D{$this->index}/F{$this->index}",
            "=B{$this->index}+D{$this->index}"
        ];

        $this->totalCssRange[] = "A{$this->index}:F{$this->index}";

        $this->index = 2;
        $this->clearIndexOfArea();

        return $this;
    }

    protected function drawThisYear()
    {
        foreach ($this->getThisYearASFormatArr() as $areaName => $areaRes) {
            $this->drawYearAreaRow($areaName, $areaRes);
        }

        $this->rows[$this->index] = array_merge($this->rows[$this->index], [
            '',
            '合計', 
            $this->getSumFormula($this->getIndexOfArea(), 'I'),
            "=I{$this->index}/M{$this->index}",
            $this->getSumFormula($this->getIndexOfArea(), 'K'),
            "=K{$this->index}/M{$this->index}",
            "=I{$this->index}+K{$this->index}"
        ]);

        $this->totalCssRange[] = "H{$this->index}:M{$this->index}";

        return $this;
    }

    protected function drawMonthEmpRow(array $storeRes)
    {
        foreach ($storeRes as $empRes) {
            $this->rows[$this->index] = [
                array_get($empRes, 'PC_NAME'), 
                (int) array_get($empRes, 'PL業績'),
                "=B{$this->index}/F{$this->index}",
                (int) array_get($empRes, 'nonPL業績'),
                "=D{$this->index}/F{$this->index}",
                "=B{$this->index}+D{$this->index}"
            ];

            $this->index ++;
        }

        return $this;
    }

    protected function drawMonthStoreRow($storeName, array $storeRes)
    {
        $start = $this->index - count($storeRes);
        $end = $this->index - 1;

        $this->rows[$this->index] = [
            $storeName, 
            "=SUM(B{$start}:B{$end})",
            "=B{$this->index}/F{$this->index}",
            "=SUM(D{$start}:D{$end})",
            "=D{$this->index}/F{$this->index}",
            "=B{$this->index}+D{$this->index}"
        ];

        $this->addIndexOfStore($this->index);
        $this->storeCssRange[] = "A{$this->index}:F{$this->index}";

        $this->index ++;

        return $this;
    }

    protected function drawMonthAreaRow($areaName, array $areaRes)
    {
        foreach ($areaRes as $storeName => $storeRes) {
            $this
                ->drawMonthEmpRow($storeRes)
                ->drawMonthStoreRow($storeName, $storeRes)
            ;                    
        }        

        $this->rows[$this->index] = [
            $areaName, 
            $this->getSumFormula($this->getIndexOfStore(), 'B'),
            "=B{$this->index}/F{$this->index}",
            $this->getSumFormula($this->getIndexOfStore(), 'D'),
            "=D{$this->index}/F{$this->index}",
            "=B{$this->index}+D{$this->index}"
        ];

        $this->addIndexOfArea($this->index);
        $this->areaCssRange[] = "A{$this->index}:F{$this->index}";
        $this->clearIndexOfStore();

        $this->index ++;
    }

    protected function drawYearStoreRow($storeName, array $storeRes)
    {
        $pl = 0;
        $nonPl = 0;
        
        foreach ($storeRes as $emp) {
            $pl += (int) array_get($emp, 'PL業績'); 
            $nonPl += (int) array_get($emp, 'nonPL業績');
        }

        $this->rows[$this->index] = array_merge($this->rows[$this->index], [
            '',
            $storeName, 
            $pl,
            "=I{$this->index}/M{$this->index}",
            $nonPl,
            "=K{$this->index}/M{$this->index}",
            "=I{$this->index}+K{$this->index}"
        ]);

        $this->index ++;

        return $this;
    }

    protected function drawYearAreaRow($areaName, array $areaRes)
    {
        foreach ($areaRes as $storeName => $storeRes) {
            $this->drawYearStoreRow($storeName, $storeRes);
        }        

        $start = $this->index - count($areaRes);
        $end = $this->index - 1;

        $this->rows[$this->index] = array_merge($this->rows[$this->index], [
            '',
            $areaName, 
            "=SUM(I{$start}:I{$end})",
            "=I{$this->index}/M{$this->index}",
            "=SUM(K{$start}:K{$end})",
            "=K{$this->index}/M{$this->index}",
            "=I{$this->index}+K{$this->index}"
        ]);

        $this->addIndexOfArea($this->index);
        $this->areaCssRange[] = "H{$this->index}:M{$this->index}";
        
        $this->index ++;

        return $this;
    }

    protected function getWidth()
    {
        return [
            'A' => 25,
            'B' => 15,
            'C' => 17,
            'D' => 15,
            'E' => 17,
            'F' => 15,
            'G' => 5,
            'H' => 30,
            'I' => 15,
            'J' => 17,
            'K' => 15,
            'L' => 17,
            'M' => 15
        ];
    }

    protected function getColumnFormat()
    {
        return [
            'B' => '#,##0', 
            'C' => '0%', 
            'D' => '#,##0', 
            'E' => '0%', 
            'F' => '#,##0', 
            'I' => '#,##0', 
            'J' => '0%', 
            'K' => '#,##0', 
            'L' => '0%', 
            'M' => '#,##0'
        ];
    }

    protected function getTopColumnName()
    {
        return ['PC_NAME', 'PL業績', 'PL業績佔比', 'nonPL業績', 'nonPL業績佔比', '業績合計', '', 'PC_NAME', 'PL業績', 'PL業績佔比', 'nonPL業績', 'nonPL業績佔比', '業績合計'];
    }

    protected function getSumFormula(array $arr, $colName)
    {
        $str = '=';

        foreach ($arr as $val) {
            $str .= "{$colName}{$val}+";
        }

        return substr($str, 0, -1);
    }

    public function getIndexOfStore()
    {
        return $this->indexOfStore;
    }

    protected function addIndexOfStore($val)
    {
        $this->indexOfStore[] = $val;

        return $this;
    }

    protected function clearIndexOfStore()
    {
        $this->indexOfStore = [];
        
        return $this;
    }

    public function getIndexOfArea()
    {
        return $this->indexOfArea;
    }

    protected function addIndexOfArea($val)
    {
        $this->indexOfArea[] = $val;

        return $this;
    }

    protected function clearIndexOfArea()
    {
        $this->indexOfArea = [];

        return $this;
    }

    protected function genASFormatAttr(array $res)
    {
        $arr = [];

        foreach ($res as $empRes) {
           $this
                ->_buildASFormatArrAreaKey($empRes, $arr)
                ->_buildASFormatArrStoreKey($empRes, $arr)
                ->_pushASFormatArr($empRes, $arr)
            ;            
        }

        return $arr;
    }

    protected function genThisMonthASFormatArr()
    {
        return $this->genASFormatAttr($this->getThisMonthEmpRes());
    }

    protected function genThisYearASFormatArr()
    {
        return $this->genASFormatAttr($this->getThisYearEmpRes());
    }

    private function _buildASFormatArrAreaKey(array $empRes, array &$arr)
    {   
        $areaName = trim(array_get($empRes, self::AREA, NULL));

        if (!array_key_exists($areaName, $arr)) {                
            $arr[$areaName] = [];
        }

        return $this;
    }

    private function _buildASFormatArrStoreKey(array $empRes, array &$arr)
    {
        $areaName = trim(array_get($empRes, self::AREA, NULL));
        $storeName = trim(array_get($empRes, self::STORE, NULL));

        if (!array_key_exists($storeName, $arr[$areaName])) {
            $arr[$areaName][$storeName] = [];
        }

        return $this;
    }

    private function _pushASFormatArr(array $empRes, array &$arr)
    {
        $areaName = trim(array_get($empRes, self::AREA, NULL));
        $storeName = trim(array_get($empRes, self::STORE, NULL));

        $arr[$areaName][$storeName][] = $empRes;

        return $this;
    }

    protected function getThisMonthRetailSaleRes()
    {        
        $startDate = with(new Carbon('first day of last month'))->format('Ymd');
        $endDate = with(new Carbon('last day of last month'))->format('Ymd');

        return Processor::getArrayResult($this->getRetailSaleQuery($startDate, $endDate), 'pos');
    }

    protected function getThisYearRetailSaleRes()
    {
        $startDate = with(new Carbon('first day of january'))->format('Ymd');
        $endDate = with(new Carbon('last day of last month'))->format('Ymd');

        return Processor::getArrayResult($this->getRetailSaleQuery($startDate, $endDate), 'pos');
    }

    /**
     * Return retailSaleQuery
     * 
     * @param  string $startDate 
     * @param  string $endDate   
     * @return string
     */
    protected function getRetailSaleQuery($startDate, $endDate)
    {
        return str_replace(['$startDate', '$endDate'], [$startDate, $endDate], file_get_contents(__DIR__ . '/../../../../storage/sql/RetailSale/person.sql'));
    }

    /**
     * Gets the value of thisMonthEmpRes.
     *
     * @return mixed
     */
    public function getThisMonthEmpRes()
    {
        return $this->thisMonthEmpRes;
    }

    /**
     * Sets the value of thisMonthEmpRes.
     *
     * @param mixed $thisMonthEmpRes the this month emp res
     *
     * @return self
     */
    protected function setThisMonthEmpRes(array $thisMonthEmpRes)
    {
        $this->thisMonthEmpRes = $thisMonthEmpRes;

        return $this;
    }

    /**
     * Gets the value of thisYearEmpRes.
     *
     * @return mixed
     */
    public function getThisYearEmpRes()
    {
        return $this->thisYearEmpRes;
    }

    /**
     * Sets the value of thisYearEmpRes.
     *
     * @param mixed $thisYearEmpRes the this year emp res
     *
     * @return self
     */
    protected function setThisYearEmpRes(array $thisYearEmpRes)
    {
        $this->thisYearEmpRes = $thisYearEmpRes;

        return $this;
    }

    /**
     * Gets the value of thisMonthASFormatArr.
     *
     * @return mixed
     */
    public function getThisMonthASFormatArr()
    {
        return $this->thisMonthASFormatArr;
    }

    /**
     * Sets the value of thisMonthASFormatArr.
     *
     * @param mixed $thisMonthASFormatArr the this month sformat arr
     *
     * @return self
     */
    protected function setThisMonthASFormatArr(array $thisMonthASFormatArr)
    {
        $this->thisMonthASFormatArr = $thisMonthASFormatArr;

        return $this;
    }

    /**
     * Gets the value of thisYearASFormatArr.
     *
     * @return mixed
     */
    public function getThisYearASFormatArr()
    {
        return $this->thisYearASFormatArr;
    }

    /**
     * Sets the value of thisYearASFormatArr.
     *
     * @param mixed $thisYearASFormatArr the this year sformat arr
     *
     * @return self
     */
    protected function setThisYearASFormatArr(array $thisYearASFormatArr)
    {
        $this->thisYearASFormatArr = $thisYearASFormatArr;

        return $this;
    }

    /**
     * Gets the value of rows.
     *
     * @return mixed
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Sets the value of rows.
     *
     * @param mixed $rows the rows
     *
     * @return self
     */
    protected function setRows($rows)
    {
        $this->rows = $rows;

        return $this;
    }
}