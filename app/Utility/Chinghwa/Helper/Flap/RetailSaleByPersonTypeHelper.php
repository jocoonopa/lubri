<?php

namespace App\Utility\Chinghwa\Helper\Flap;

class RetailSaleByPersonTypeHelper 
{
	const STORE = '門市';
	const AREA = '分區';

	protected $stores;
	protected $areas;
	protected $tree;
	protected $rows;
	protected $areaRows;

	public function __construct(array $emps)
	{
		$this->setAreas($emps)->setTree($emps)->setRows($emps);
	}

	public function getRows()
	{
		return $this->rows;
	}

	public function setTree(array $emps)
	{
		$tmp = [];

		foreach ($this->getAreas() as $area) {
			$tmp[$area] = [];
		}

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

	public function setRows(array $emps)
	{
		$rows = [];
		$areaRows = [];

		$rows[] = $this->genHeadRow();

		foreach ($this->getTree() as $areaName => $areaPak) {
			$row = $this->genRowByArea($emps, $areaName);
			$this->setRowCssStyle($row, ['backgroundColor' => '#000000', 'color' => '#ffffff', 'fontWeight' => 'bold']);
			$rows[] = $row;
			$areaRows[] = $row;

			foreach ($areaPak as $storeName => $storePak) {
				$row = $this->genRowByStore($emps, $storeName);				
				$this->setRowCssStyle($row, ['backgroundColor' => '#C19B69', 'color' => '#ffffff', 'fontWeight' => 'bold']);
				$rows[] = $row;

				foreach ($storePak as $emp) {
					$this->cal($emp);
					$rows[] = $emp;
				}
			}
		}

		$row = $this->combineRows($areaRows);
		$this->setRowCssStyle($row, ['backgroundColor' => '#000000', 'color' => '#ffffff', 'fontWeight' => 'bold']);

		$rows[] = $row;
		$this->rows = $rows;
		//dd($this->rows);
		return $this;
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

	// PL業績, PL業績佔比, nonPL業績, NonPL業績佔比, 業績, 佔比
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