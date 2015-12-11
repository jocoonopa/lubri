<?php

namespace App\Http\Controllers\Compare;

use Validator;
use Input;
use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use App\Utility\Chinghwa\Compare\HoneyBaby;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FinancialStrikeBalanceController extends Controller
{
	public function index()
    {
        return view('compare.financialStrikeBalance.index');
    }

    public function donsun()
    {
    	$excel = \App::make('excel');
    	$dispatchFile = $excel->selectSheets('出貨')->load($this->getDonsunFilePath());
        $turnbackFile = $excel->selectSheets('退貨')->load($this->getDonsunFilePath());

        $dispatchCodes = $this->fetchCodesFromRow($dispatchFile->skip(1)->get([2]));
        $turnbackCodes = $this->fetchCodesFromRow($turnbackFile->skip(1)->get([2]));

        pr(array_intersect($dispatchCodes, $turnbackCodes));

        echo "count:dispatchFile".count($dispatchCodes)."<br>";
        echo "count:turnbackCodes".count($turnbackCodes)."<br>";

        $dispatchCodes = array_diff($dispatchCodes, $turnbackCodes);

        echo "count:after_process_dispatchFile".count($dispatchCodes)."<br>";

        foreach ($dispatchCodes as $key => $code) {
        	echo "{$code}<br>";
        }

    	return 'donsun';
    }

    protected function fetchCodesFromRow($obj)
    {
    	$returns = [];
    	foreach ($obj as $row) {
    		$returns[] = $row[0];
    	}

    	return $returns;
    }

    /**
     * 取得東森檔案路徑
     * 
     * @return string
     */
    protected function getDonsunFilePath()
    {
        return  __DIR__ . '/../../../../storage/excel/example/10407tonsan.xlsx';
    }
}