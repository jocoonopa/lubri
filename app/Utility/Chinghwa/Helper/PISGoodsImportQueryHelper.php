<?php

namespace App\Utility\Chinghwa\Helper;

class PISGoodsImportQueryHelper
{
	const GOODS_SERNO_LENGTH = 26;
	const GOODS_SERNO_NUMBER_PART_LENGTH = 21;
	const GOODS_SERNO_PREFIX = 'GOODS';

	protected $currentSerNo;

	public function setCurrentSerNo($val)
	{
		$this->currentSerNo = $val;

		return $this;
	}

	public function getCurrentSerNo()
	{
		return $this->currentSerNo;
	}

	public function genSelectQuery()
	{
		$sql = "SELECT * FROM PIS_Goods WHERE Code IN {$this->genInPartialQuery($this->getCodeList())} ORDER BY CRT_TIME DESC";

		return $sql;
	}

	protected function genInPartialQuery(array $codes)
	{
		$sql = '(';

		foreach ($codes as $code) {
			$sql .= "'{$code}',";
		}

		return substr($sql, 0, -1) . ')';
	}

	public function getCodeList()
	{
		return json_decode(file_get_contents(__DIR__ . '/../../../../storage/json/goodslist.json'), true);
	}

	public function genInsertQuery(array $row, &$lastSerNo)
	{
		$insertColumnQueryString = $this->genInsertColumnString();

		$sql = "INSERT INTO PIS_Goods {$insertColumnQueryString} VALUES ";

		return $sql . $this->genInsertValuesString($row, $lastSerNo);
	}

	protected function genInsertValuesString(array $row, &$lastSerNo)
	{
		$sql = '(';

		foreach ($this->getColumns() as $column) {
			$sql .= $this
				->rowCodeModify($row, $column)
				->rowSerNoModify($row, $column, $lastSerNo)
				->rowPriceModify($row, $column)
				->rowNameModify($row, $column)
				->getSqlCell($row, $column)
			;
		}

		return substr($sql, 0, -1) . ')';
	}

	protected function rowCodeModify(&$row, $column)
	{
		if ($this->isCode(strtoupper($column))) {
			$row[$column] = "CT{$row[$column]}";
		}

		return $this;
	}

	protected function rowSerNoModify(&$row, $column, &$lastSerNo)
	{
		if ($this->isSerNo(strtoupper($column))) {
			$row[$column] = $this->getNextGoodsSerNo($lastSerNo);
		}

		return $this;
	}

	protected function rowPriceModify(&$row, $column)
	{
		if ($this->isPrice(strtoupper($column))) {
			$row[$column] += 100;
		}

		return $this;
	}

	protected function rowNameModify(&$row, $column)
	{
		if ($this->isName(strtoupper($column))) {
			$row[$column] = str_replace("'", "''", $row[$column]);
		}

		return $this;
	}

	protected function getSqlCell($row, $column)
	{
		return ('' === $row[$column]) ? 'NULL,' : "'{$row[$column]}',";
	}

	protected function isCode($val)
	{
		$list = [
			'CODE',
			'BARCODE',
			'GOODSSTYLECODE'
		];

		return in_array($val, $list);
	}

	protected function isSerNo($val)
	{
		return 'SERNO' === $val;
	}

	protected function isPrice($val)
	{
		$list = [
			'COSTPRICE', 'TAXEDCOSTPRICE',
			'LISTPRICE', 'TAXEDLISTPRICE',
			'UPSETPRICE', 'TAXEDUPSETPRICE',
			'PRICE', 'TAXEDPRICE',
			'GOLDPRICE', 'TAXEDGOLDPRICE',
			'SILVERPRICE', 'TAXEDSILVERPRICE',
			'PLATINMPRICE', 'TAXEDPLATINMPRICE',
			'DEPARTPRICE', 'TAXEDDEPARTPRICE',
			'EQUALPRICE', 'TAXEDEQUALPRICE'
		];

		return in_array($val, $list);
	}

	protected function isName($val)
	{
		$list = [
			'Name',
			'InvoiceName',
			'SpecName'
		];

		return in_array($val, $list);
	}

	public function genFetchLastSerNoQuery()
	{
		$sql = 'SELECT TOP 1 SerNo FROM PIS_Goods ORDER BY CRT_TIME DESC';

		return $sql;
	}

	/**
	 * 產編SPECGOODS, 14個0, 7 數字
	 */
	public function getNextGoodsSerNo(&$SerNo)
	{
		if (self::GOODS_SERNO_LENGTH !== strlen($SerNo)) {
			throw new \Exception("Illegal {$SerNo}");
		}

		$num = (int) preg_replace('/\D/', '', $SerNo);

		$this->setCurrentSerNo(self::GOODS_SERNO_PREFIX . str_pad(++ $num, self::GOODS_SERNO_NUMBER_PART_LENGTH, '0', STR_PAD_LEFT));

		return $SerNo = $this->getCurrentSerNo();
	}

	protected function genInsertColumnString()
	{
		$columns = $this->getColumns();

		return str_replace("'", '', $this->genInPartialQuery($columns));
	}

	protected function getColumns()
	{
		return $columns = [
			'SerNo',
			'Code',
			'Name',
			'GoodsStyleCode',
			'SpecName',
			'ColorSerNo',
			'SizeSerNo',
			'SizeTypeIndexSerNo',
			'SellYear',
			'SellSeason',
			'LargeCategorySerNo',
			'MiddleCategorySerNo',
			'SmallCategorySerNo',
			'BrandSerNo',
			'MiddleBrandSerNo',
			'InventoryCycle',
			'InvoiceName',
			'Barcode',
			'InternationalBarcode',
			'ModelCode',
			'GoodsPropertiesSerNo',
			'UnitSerNo',
			'BigUnitSerNo',
			'BigExchangeRate',
			'MiddleUnitSerNo',
			'MiddleExchangeRate',
			'GoodsSource',
			'GoodsType',
			'IsCalculate',
			'IsStop',
			'StopDate',
			'LeadDay',
			'PurchaseBatchAmount',
			'IsSupplierConsignment',
			'DullDay',
			'OrderBatchAmount',
			'KeyInDate',
			'MainSupplierSerNo',
			'EName',
			'ESpecName',
			'ProductPlaceSerNo',
			'Remark',
			'Remark2',
			'Remark3',
			'PurchaseCurrencySerNo',
			'PurchaseRate',
			'PurchaseTaxType',
			'CostPrice',
			'TaxedCostPrice',
			'SellCurrencySerNo',
			'SellRate',
			'SellTaxType',
			'ListPrice',
			'TaxedListPrice',
			'UpsetPrice',
			'TaxedUpsetPrice',
			'Price',
			'TaxedPrice',
			'GoldPrice',
			'TaxedGoldPrice',
			'SilverPrice',
			'TaxedSilverPrice',
			'PlatinmPrice',
			'TaxedPlatinmPrice',
			'DepartPrice',
			'TaxedDepartPrice',
			'EqualPrice',
			'TaxedEqualPrice'
		];
	}
}