<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\ExportExcel;
use App\Utility\Chinghwa\Helper\Excel\ExcelHelper;
use App\Utility\Chinghwa\Helper\Flap\RetailSaleByPersonTypeHelper;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Input;
use Carbon\Carbon;

class RetailSaleByPersonTypeController extends Controller
{
	public function process()
	{
		$self = $this;
		$startDate = new Carbon('first day of last month');
		$emps = Processor::getArrayResult($this->getQb($startDate, new Carbon('last day of last month')), 'Pos');
		$helper = new RetailSaleByPersonTypeHelper($emps);
		$rows = $helper->getRows();
		$subject = '門市營業額分析月報表-人' . $startDate->format('Ym');

		Excel::create('Retail_Sale_PersonType_' . $startDate->format('Ym'), function($excel) use ($rows) {
		    $excel->sheet('報表', function($sheet) use ($rows) {
		    	$sheet
	                ->setAutoSize(true)
	                ->setFontFamily(ExportExcel::FONT_DEFAULT)
	                ->setFontSize(12)
	                ->setColumnFormat([
	                	'A' => '@',
	                	'B' => '@',	          
	                	'C' => '0%', 
	                	'D' => '@',
	                	'E' => '0%',
	                	'F' => '@',
	                	'G' => '0%',
	                ])
	                ->freezeFirstRow()
	            ; 

		    	foreach ($rows as $index => $row) {
		    		$_index = $index + 1;
		    		$sheet->cells("A{$_index}:G{$_index}", function($cells) use ($row, $_index) {
		    			if (array_key_exists('backgroundColor', $row)) {
		    				$cells->setBackground($row['backgroundColor']);
		    			}

		    			if (array_key_exists('color', $row)) {
		    				$cells->setFontColor($row['color']);
		    			}

		    			if (array_key_exists('fontWeight', $row)) {
		    				$cells->setFontWeight($row['fontWeight']);
		    			}

		    			if (1 === $_index) {
		    				$cells->setFontWeight('bold');
		    				$cells->setFontSize(14);
		    			}
					});

		    		if (1 === $_index) {
		    			$sheet->appendRow($_index, [$row['PC_NAME'], $row['PL業績'], $row['PL業績佔比'], $row['nonPL業績'], $row['nonPL業績佔比'], $row['業績'], $row['佔比']]);		    			
		    		}  else {
		    			$sheet->appendRow(
		    				$_index, 
		    				[
		    					$row['PC_NAME'], 
		    					number_format((int) $row['PL業績']), 
		    					$row['PL業績佔比'], 
		    					number_format((int) $row['nonPL業績']), 
		    					$row['nonPL業績佔比'], 
		    					number_format((int) $row['業績']), 
		    					$row['佔比']
		    				]
		    			);
		    		}
		    	}
		    });

		})->store('xls', storage_path('excel/exports'));

 		Mail::send('emails.creditCard', ['title' => $subject], function ($m) use ($subject, $startDate, $self) {
            $m->subject($subject)->attach(__DIR__ . '/../../../../storage/excel/exports/Retail_Sale_PersonType_' . $startDate->format('Ym') . '.xls');

            foreach ($self->getToList() as $email => $name) {
                $m->to($email, $name);
            }

            foreach ($self->getCCList() as $email => $name) { 
                $m->cc($email, $name);
            }
        });

        return '門市營業額分析月報表-人 Send Complete!';
	}

	protected function getToList()
    {
        return [
            'lingying3025@chinghwa.com.tw' => '6521吳俐穎',
            'meganlee@chinghwa.com.tw' => '6500李惠淑'
        ];
    }

    protected function getCCList()
    {
        return [
            'sl@chinghwa.com.tw' => '6700莊淑玲',
            'swhsu@chinghwa.com.tw' => '6800徐士偉',
            'tonyvanhsu@chinghwa.com.tw' => '6820徐士弘',
            'jocoonopa@chinghwa.com.tw' => '6231小閎'
        ];
    }

	public function getQb(Carbon $startDate, Carbon $endDate)
	{
		return str_replace(['$startDate', '$endDate'], [$startDate->format('Ymd'), $endDate->format('Ymd')], file_get_contents(__DIR__ . '/../../../../storage/sql/RetailSale/person.sql'));
	}
}