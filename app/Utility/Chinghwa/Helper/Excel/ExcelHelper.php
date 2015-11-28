<?php

namespace App\Utility\Chinghwa\Helper\Excel;

use Maatwebsite\Excel\Facades\Excel;
use App\Utility\Chinghwa\ExportExcel;
use App\Utility\Chinghwa\Database\Connectors\Connector;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;

class ExcelHelper
{
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
    public static function initExcel(\Maatwebsite\Excel\Writers\LaravelExcelWriter $excel)
    {
        $returnSheet = NULL;

        $excel
            ->setTitle(ExportExcel::M64_TITLE)
            ->setCreator(ExportExcel::CREATOR)
            ->setCompany(ExportExcel::COMPANY)
        ;

        $excel->sheet('表格1', self::getInitCallback($returnSheet));

        return $returnSheet;
    }

    protected static function getInitCallback(&$returnSheet)
    {
    	return function($sheet) use (&$returnSheet) {
            $sheet
                ->setAutoSize(true)
                ->setFontFamily('微軟正黑體')
                ->setFontSize(12)
                ->row(\PHPExcel_Worksheet::BREAK_ROW, $self->getSheetHead())
                ->setColumnFormat($this->getColumnFormat('A', 'H', '@'))
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
        };
    }

    protected static function getColumnFormat($start = 'A', $end = 'Z', $format = '@')
	{
	    $columnFormatArr = [];

	    foreach (range($start, $end) as $char) {
	        $columnFormatArr[$char] = $format;
	    }

	    return $columnFormatArr;
	}

    public static function rmi($str)
    {
        $rowIndex = 0;
        $arr = str_split(strrev($str));

        foreach ($arr as $key => $char) {           
            $rowIndex += (ord(strtoupper($char)) - ExportExcel::ASCII_NUMBER_A) * pow(ExportExcel::ALPHABET_NUMBERS, $key);
        }

        return $rowIndex - 1;
    }

    public static function genBasicSheet(&$excel, $sheetName, $columnFormatArray, $borderRange, $query, $headArray)
    {
        $excel->sheet($sheetName, self::getBasicSheetCallback($columnFormatArray, $borderRange, $query, $headArray));
    }

    protected static function getBasicSheetCallback($columnFormatArray, $borderRange, $query, $headArray)
    {
    	return function($sheet) use ($columnFormatArray, $borderRange, $query, $headArray) {
            $sheet
                ->setAutoSize(true)
                ->setFontFamily(ExportExcel::FONT_DEFAULT)
                ->setFontSize(12)
                ->setColumnFormat($columnFormatArray)
                ->freezeFirstRow()
            ; 

            $sheet->cells('A1:' . $borderRange . '1', function ($cells) {
                $cells->setBackground('#000000')->setFontColor('#ffffff')->setAlignment('center');
            });

            if ($res = Processor::execErp($query)) {
                $i = 0;
                $sheet->row(++ $i, $headArray);

                while ($row = odbc_fetch_array($res)) {
                    c8res($row);

                    $sheet->row(++ $i, $row);
                }
            }
        };
    }
}	