<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use App\Utility\Chinghwa\RS\Row;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Input;

class CTILayoutController extends Controller
{
	public function index()
    {
        $button = '<a href="' . route('ctilayout_download')  .'" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-floppy-save"></i></a>';
        
        return view('basic.simple', [
            'title' => 'CTI的Import Layout', 
            'des' => '<h4>CTI的Import Layout   ' . $button . '</h4><pre>' . $this->getQuery() . '</pre>',
            'res' => ''       
        ]);
    }

    public function download()
    {        
        $self = $this;

        return Excel::create($this->getFileName(), function ($excel) use ($self) {
            $self
                ->genBasicSheet($excel, 'sheet', [], 'AE', $self->getQuery(), $self->getExportHead())              
            ;
        })->download('xls');
    }

    protected function getQuery()
    {
        return file_get_contents(__DIR__ . '/../../../../storage/sql/CTILayout.sql');
    }

    protected function getFileName()
    {
        $date = new \DateTime;
        //$date->modify('-1 month');

        return ExportExcel::WAYTER_IMPORTLAYOUT_FILENAME . '_' . $date->format('Ymd');
    }

    protected function getFilePath()
    {
        return __DIR__ . '/../../../../storage/excel/exports/' . $this->getFileName() .  '.xls';
    }

    protected function getSubject()
    {
        $date = new \DateTime;
        //$date->modify('-1 month');

        return 'CTI的Import Layout' . $date->format('Ym');
    }

    protected function getExportHead()
    {
        return [
            '會員代號', 
            '會員姓名', 
            '性別', 
            '生日', 
            '身份證號', 
            '連絡電話', 
            '公司電話', 
            '手機號碼', 
            '縣市', 
            '區', 
            '郵遞區號', 
            '地址', 
            'e-mail', 
            '開發人代號', 
            '開發人姓名', 
            '會員類別代號', 
            '會員類別名稱', 
            '區別代號', 
            '區別名稱', 
            '首次購物金額', 
            '首次購物日', 
            '最後購物金額', 
            '最後購物日', 
            '累積購物金額', 
            '累積紅利點數', 
            '輔翼會員參數', 
            '預產期', 
            '醫院', 
            // '生日-年', 
            // '生日-月', 
            // '生日-日'
        ];
    }

    public function genBasicSheet(&$excel, $sheetName, $columnFormatArray, $borderRange, $query, $headArray)
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
                        $row = array_values($row);
                        $self->c8res($row);
                        $data = $self->getRefactRow($row);
                        $sheet->row(++ $i, $data);
                    }
                }
            });
        
        return $this;
    }

    protected function getRefactRow($row)
    {
        $data = [];
        $limit = 26;

        for ($i = 0; $i < $limit; $i ++) { 
            $data[$i] = $row[$i];
        }

        $he = $this->getHospitalAndEdate($row[$this->rmi('AA')]);
        //$splitBirthday = $this->getSplitBirthDay($row[$this->rmi('D')]);

        $data[$this->rmi('AA')] = $he['edate'];
        $data[$this->rmi('AB')] = $he['hospital'];
        // $data[$this->rmi('AC')] = $splitBirthday['y'];
        // $data[$this->rmi('AD')] = $splitBirthday['m'];
        // $data[$this->rmi('AE')] = $splitBirthday['d'];

        return $data;
    }

    protected function getHospitalAndEdate($text)
    {
        $arr = explode(':', $text);

        if (3 > count($arr)) {
            return ['hospital' => '', 'edate' => ''];
        }

        return [
            'hospital' => str_replace('生產醫院:', '', $arr[2]),
            'edate' => preg_replace('/[^0-9]/', '', $arr[1])
        ];
    }

    protected function getSplitBirthDay($birthday)
    {
        if (8 !== strlen($birthday)) {
            return ['y' => '', 'm' => '', 'd' => ''];
        }

        return [
            'y' => substr($birthday, 0, 4),
            'm' => substr($birthday, 4, 2),
            'd' => substr($birthday, 6, 2)
        ];
    }
}