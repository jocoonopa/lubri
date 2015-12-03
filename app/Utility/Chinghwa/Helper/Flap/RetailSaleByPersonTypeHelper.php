<?php

namespace App\Utility\Chinghwa\Helper\Flap;

use App\Utility\Chinghwa\ExportExcel;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class RetailSaleByPersonTypeHelper 
{
	const STORE        = '門市';
	const AREA         = '分區';
	const FILENAME     = 'Retail_Sale_PersonType_';
	const TITLE        = '門市營業額分析月報表-人';
	const COMPLETE_MSG = self::TITLE . 'Send Complete';

	protected $stores;
	protected $areas;
	protected $tree;
	protected $rows;
	protected $areaRows;
	protected $startDate;

	public function __construct(array $emps, Carbon $startDate)
	{
		$this
			->setStartDate($startDate)
			->setAreas($emps)
			->setTree($emps)
			->setRowsByIterateTree($emps)
		;
	}

	protected function setStartDate(Carbon $startDate)
	{
		$this->startDate = $startDate;

		return $this;
	}

	protected function getStartDate()
	{
		return $this->startDate;
	}

	public function getRows()
	{
		return $this->rows;
	}

	public function setTree(array $emps)
	{
		$tmp = array_declare($this->getAreas(), []);

		foreach ($emps as $emp) {
			if (!isset($tmp[$emp[self::AREA]][$emp[self::STORE]])) {
				$tmp[$emp[self::AREA]][$emp[self::STORE]] = [];
			}

			$tmp[$emp[self::AREA]][$emp[self::STORE]][] = $emp;
		}

		$this->tree = $tmp;

		return $this;
	}

	public function setAreas(array $emps)
	{
		return $this->set($emps, self::AREA);
	}	

	public function getAreas()
	{
		return $this->areas;
	}	

	public function getTree()
	{
		return $this->tree;
	}

	public static function getFileRealPathWithDate($dateString)
	{
		return __DIR__ . '/../../../../../storage/excel/exports/' . self::FILENAME . "{$dateString}.xls";
	}

	public function setRowsByIterateTree(array $emps)
	{
		$this->rows = [$this->genHeadRow()];

		foreach ($this->getTree() as $areaName => $areaPak) {
			$this->handleAreaRow($emps, $areaName);

			foreach ($areaPak as $storeName => $storePak) {
				$this->handleStoreRow($emps, $storeName);

				foreach ($storePak as $emp) {
					$this->handleEmpRow($emp);
				}
			}
		}

		$this->handleTotalRow();
		
		return $this;
	}

	protected function handleAreaRow(array $emps, $areaName)
	{
		$row = $this->genRowByArea($emps, $areaName);
		$row['isArea'] = true;
		
		$this->setRowCssStyle($row, ['backgroundColor' => '#000000', 'color' => '#ffffff', 'fontWeight' => 'bold']);
		$this->rows[] = $row;

		return $this;
	}

	protected function handleStoreRow(array $emps, $storeName)
	{
		$row = $this->genRowByStore($emps, $storeName);				
		$this->setRowCssStyle($row, ['backgroundColor' => '#C19B69', 'color' => '#ffffff', 'fontWeight' => 'bold']);
		$this->rows[] = $row;

		return $this;
	}

	protected function handleEmpRow(array $emp)
	{
		$this->cal($emp);
		$this->rows[] = $emp;

		return $this;
	}

	protected function handleTotalRow()
	{
		$row = $this->combineRows($this->getAreaRows());
		$this->setRowCssStyle($row, ['backgroundColor' => '#000000', 'color' => '#ffffff', 'fontWeight' => 'bold']);

		return $this;
	}

	protected function getAreaRows()
	{
		return array_filter($this->getRows(), function($row) {
		    return isset($row['isArea']);
		}, ARRAY_FILTER_USE_BOTH);
	}

	public function createAndStore()
	{
		return Excel::create(
			self::FILENAME . $this->startDate->format('Ym'), 
			$this->getExcelCallBackFun($this->getRows())
		)->store('xls', storage_path('excel/exports'));
	}

	protected function getExcelCallBackFun($rows)
	{
		return function($excel) use ($rows) {
		    $excel->sheet('報表', function($sheet) use ($rows) {
		    	$this->setBasicSheetProperty($sheet);

		    	foreach ($rows as $index => $row) {
		    		$sheet->cells("A{$index}:G{$index}", $this->getSetCssStyleCallback($row, ++ $index));
		    		
		    		$sheet->appendRow($index, $this->getRowAppendData($row, $index));		    			
		    	}
		    });
		};
	}

	protected function setBasicSheetProperty(&$sheet)
	{
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

        return $this;
	}

	protected function setCellsCss(&$cells, array $row)
	{
		if (array_key_exists('backgroundColor', $row)) {
			$cells->setBackground($row['backgroundColor']);
		}

		if (array_key_exists('color', $row)) {
			$cells->setFontColor($row['color']);
		}

		if (array_key_exists('fontWeight', $row)) {
			$cells->setFontWeight($row['fontWeight']);
		}

		return $this;
	}

	protected function setHeaderCellsFontStyle(&$cells)
	{
		$cells->setFontWeight('bold');
		$cells->setFontSize(14);

		return $this;
	}

	protected function isHead($index)
	{
		return (1 === $index);
	}

	protected function getSetCssStyleCallback($row, $index)
	{
		return function($cells) use ($row, $index) {
			if ($this->isHead($index)) {
				$this->setHeaderCellsFontStyle($cells);
			}

			$this->setCellsCss($cells, $row);
		};
	}

	protected function getRowAppendData(array $row, $index)
	{
		return ($this->isHead($index)) 
			? [$row['PC_NAME'], $row['PL業績'], $row['PL業績佔比'], $row['nonPL業績'], $row['nonPL業績佔比'], $row['業績'], $row['佔比']]
			: [$row['PC_NAME'],number_format((int) $row['PL業績']), $row['PL業績佔比'], number_format((int) $row['nonPL業績']), $row['nonPL業績佔比'], number_format((int) $row['業績']), $row['佔比']]
		;
	}

	protected function setRowCssStyle(array &$row, array $cssArr)
	{
		foreach ($cssArr as $key => $css) {
			$row[$key] = $css;	
		}

		return $this;
	}

	protected function set(array $emps, $key)
	{
		$r = ['areas' => self::AREA, 'stores' => self::STORE];

		$index = array_search($key, $r);

		if ($index) {
			$this->$index = $this->genA($emps, $key);
		}

		return $this;
	}

	protected function genA(array $emps, $key)
	{
		$a = [];

		foreach ($emps as $emp) {
			if (!in_array($emp[$key], $a)) {
				$a[] = $emp[$key];
			}
		}

		return $a;
	}

	public function genRowByArea(array $emps, $areaName)
	{
		$row = $this->genRowProcess($emps, $areaName, self::AREA);

		return $row;
	}

	public function genRowByStore(array $emps, $storeName)
	{
		$row = $this->genRowProcess($emps, $storeName, self::STORE);

		return $row;
	}

	protected function genHeadRow()
	{
		$row = $this->getRowPrototype();

		foreach ($row as $key => $ele) {
			$row[$key] = $key;
		}

		return $row;
	}

	public function combineRows(array $rows)
	{
		$totalRow = $this->getRowPrototype();
		$totalRow['PC_NAME'] = '總計';

		foreach ($rows as $row) {
			$this->accumulate($totalRow, $row);
		}

		return $this->cal($totalRow);
	}

	protected function getColumns()
	{
		return ['PC_NAME', 'PL業績', 'PL業績佔比', 'nonPL業績', 'nonPL業績佔比', '業績', '佔比'];
	}

	protected function getRowPrototype()
	{
		$row = [];

		foreach ($this->getColumns() as $column) {
			$row[$column] = '0';
		}

		return $row;
	}

	protected function genRowProcess(array $emps, $name, $key)
	{
		$row = $this->getRowPrototype();
		$row['PC_NAME'] = $name;
		
		foreach ($emps as $emp) {
			if ($name === $emp[$key]) {
				$this->accumulate($row, $emp);
			}
		}

		return $this->cal($row);
	}

	protected function accumulate(array &$row, array $emp)
	{
		$row['PL業績'] += $emp['PL業績'];
		$row['nonPL業績'] += $emp['nonPL業績'];
		$row['業績'] += $emp['業績'];

		return $row;
	}

	protected function cal(array &$row)
	{
		if (0 == $row['業績']) {
			return $row;
		}

		$row['PL業績佔比'] = $row['PL業績']/$row['業績'];
		$row['nonPL業績佔比'] = $row['nonPL業績']/$row['業績'];
		$row['佔比'] = 1;

		return $row;
	}
}