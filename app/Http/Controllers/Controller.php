<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Maatwebsite\Excel\Facades\Excel;
use App\Utility\Chinghwa\ExportExcel;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

    protected $queryReplaceWordsArray = array('-', '(', ')', '　', '\'');
    protected $cnx;

    const ASCII_NUMBER_A = 64;
    const ALPHABET_NUMBERS = 26;

    protected function connectToErp()
    {
        if ($this->cnx) {
            return $this->cnx;
        }

        if (!($this->cnx = odbc_connect(env('ODBC_ERP_DSN'), env('ODBC_ERP_USER'), env('ODBC_ERP_PWD'), SQL_CUR_USE_ODBC))) {
            throw new \Exception('odbc error');
        }

        return $this->cnx;
    }

    protected function connectToPos()
    {
        if ($this->cnx) {
            return $this->cnx;
        }

        if (!($this->cnx = odbc_connect(env('ODBC_POS_DSN'), env('ODBC_POS_USER'), env('ODBC_POS_PWD'), SQL_CUR_USE_ODBC))) {
            throw new \Exception('odbc error');
        }

        return $this->cnx;
    }

    protected function c8($str)
    {
        return mb_convert_encoding($str, 'UTF-8', 'big5');
    }

    protected function cb5($str)
    {
        return  mb_convert_encoding($str, 'big5', 'UTF-8');
    }

    protected function getColumnFormat($start = 'A', $end = 'Z', $format = '@')
    {
        $columnFormatArr = [];

        foreach (range($start, $end) as $char) {
            $columnFormatArr[$char] = $format;
        }

        return $columnFormatArr;
    }

    protected function srp($str, $placeholder = array())
    {
        return str_replace($this->queryReplaceWordsArray, $placeholder, $str);
    }

    protected function genInQuery($data)
    {
        $str = ' (';

        foreach ($data as $val) {
            $str .= '\'' . $val . '\',';
        }

        $str = substr($str, 0, -1) . ')';

        return $str;
    }

    protected function genQueryNestReplace(array $targetArr, array $replaceArr, $columnName)
    {
        $string = '';

        foreach ($targetArr as $key => $target) {
            $target = ('\'' === $target) ? $target . '\'' : $target;
            $columnName = (0 === $key) ? $columnName : $string; 

            $string = 'REPLACE('. $columnName .', \'' . $target . '\', \'' . $this->getArrayVal($replaceArr, $key) . '\')';
        }

        return $string;
    }

    protected function getArrayVal($arr, $key, $default = '')
    {
        return array_key_exists($key, $arr) ? $arr[$key] : $default;
    }

    protected function getRowVal($row, $key, $default = '')
    {
        return isset($row[$key]) ? $row[$key] : $default;
    }

    /**
     * Layout
     *
     * 'A' => 暫時編號
     * 'B' => 會員編號
     * 'C' => 開發人員姓名
     * 'D' => 客戶姓名
     * 'E' => 客戶電話
     * 'F' => 客戶地址
     * 'G' => 紅利
     * 'H' => 備註
     * 
     * @param  Maatwebsite\Excel\Facades\Excel $excel
     * @return $sheet
     */
    protected function initExcel(\Maatwebsite\Excel\Writers\LaravelExcelWriter $excel)
    {
        $self = $this;
        $returnSheet = NULL;

        $excel
            ->setTitle(ExportExcel::M64_TITLE)
            ->setCreator(ExportExcel::CREATOR)
            ->setCompany(ExportExcel::COMPANY)
        ;

        $excel->sheet('表格1', function($sheet) use ($self, &$returnSheet) {
            $sheet
                ->setAutoSize(true)
                ->setFontFamily('微軟正黑體')
                ->setFontSize(12)
                ->row(\PHPExcel_Worksheet::BREAK_ROW, $self->getSheetHead())
                ->setColumnFormat($self->getColumnFormat('A', 'H', '@'))
                ->setBorder('A1:H1', 'thin')
                ->freezeFirstRow()
            ; 

            $sheet->cells('A1:H1', function ($cells) {
                $cells
                    ->setBackground('#88A6E4')
                    ->setAlignment('center')
                ;
            });

            $returnSheet = $sheet;
        });

        return $returnSheet;
    }

    protected function rmi($str)
    {
        $rowIndex = 0;
        $arr = str_split(strrev($str));

        foreach ($arr as $key => $char) {           
            $rowIndex += (ord(strtoupper($char)) - self::ASCII_NUMBER_A) * pow(self::ALPHABET_NUMBERS, $key);
        }

        return $rowIndex - 1;
    }

    public function pr($v)
    {
        echo "<pre>"; print_r($v); echo "</pre>";
    }

    protected function c8res(&$row)
    {
        foreach ($row as $key => $value) {
            $row[$key] = $this->c8($value);
        }

        return $this;
    }

    protected function cb5res(&$row)
    {
        foreach ($row as $key => $value) {
            $row[$key] = $this->cb5($value);
        }

        return $this;
    }

    protected function genBasicSheet(&$excel, $sheetName, $columnFormatArray, $borderRange, $query, $headArray)
    {
        $self = $this;

        $excel->sheet($sheetName, function($sheet) use ($self, $columnFormatArray, $borderRange, $query, $headArray) {
                $sheet
                    ->setAutoSize(true)
                    ->setFontFamily(ExportExcel::FONT_DEFAULT)
                    ->setFontSize(12)
                    ->setColumnFormat($columnFormatArray)
                    ->setBorder('A1:' . $borderRange .'1', ExportExcel::BOLDER_DEFAULT)
                    ->freezeFirstRow()
                ; 

                $sheet->cells('A1:' . $borderRange . '1', function ($cells) {
                    $cells->setBackground('#000000')->setFontColor('#ffffff')->setAlignment('center');
                });

                if ($res = odbc_exec($self->connectToErp(), $self->cb5($query))) {
                    $i = 0;
                    $sheet->row(++ $i, $headArray);

                    while ($row = odbc_fetch_array($res)) {
                        $self->c8res($row);

                        $sheet->row(++ $i, $row);
                    }
                }
            });
        
        return $this;
    }
}
