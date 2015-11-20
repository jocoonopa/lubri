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
		// , 'D00115'
		$sql = "SELECT * FROM PIS_Goods WHERE Code IN " . $this->genInPartialQuery($this->getCodeList()) . " ORDER BY CRT_TIME DESC";

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

	protected function getCodeList()
	{
		return [
			'A00174', 
			'D00016'
		];
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
			switch (strtoupper($column)) {
				case 'CODE':
				case 'BARCODE':
				case 'GOODSSTYLECODE':
					$row[$column] = "CT{$row[$column]}";
					break;

				case 'SERNO':
					$row[$column] = $this->getNextGoodsSerNo($lastSerNo);
					break;

				case 'COSTPRICE':
				case 'TAXEDCOSTPRICE':
				case 'LISTPRICE':
				case 'TAXEDLISTPRICE':
				case 'UPSETPRICE':
				case 'TAXEDUPSETPRICE':
				case 'PRICE':
				case 'TAXEDPRICE':
				case 'GOLDPRICE':
				case 'TAXEDGOLDPRICE':
				case 'SILVERPRICE':
				case 'TAXEDSILVERPRICE':
				case 'PLATINMPRICE':
				case 'TAXEDPLATINMPRICE':
				case 'DEPARTPRICE':
				case 'TAXEDDEPARTPRICE':
				case 'EQUALPRICE':
				case 'TAXEDEQUALPRICE':
					$row[$column] += 100;

				default:
					break;
			}

			$sql .= ('' === $row[$column]) ? 'NULL,' : "'{$row[$column]}',";
		}

		return substr($sql, 0, -1) . ')';
	}

	public function genFetchLastSerNoQuery()
	{
		$sql = 'SELECT TOP 1 SerNo FROM PIS_Goods ORDER BY CRT_TIME DESC';

		return $sql;
	}

	/**
	 * 產編SPECGOODS, 14個0, 7 數字
	 *
	 * 
	 * @param  [type] &$SerNo [description]
	 * @return [type]         [description]
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

		return $this->genInPartialQuery($columns);
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