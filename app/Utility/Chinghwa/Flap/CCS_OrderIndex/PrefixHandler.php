<?php

namespace App\Utility\Chinghwa\Flap\CCS_OrderIndex;

use App\Utility\Chinghwa\Database\Query\Processors\Processor;

class PrefixHandler
{
	const COMETRUES_PREFIX     = 'CT';
	const ORDERINDEX_TABLENAME = 'CCS_OrderIndex';
	const ORDERNO_KEY          = 'OrderNo';
	const SERNO_KEY            = 'SerNo';
	const DIVNO_KEY            = 'No';

	protected $modifyOrders = [];

	public function execModifyOrderNos()
	{
		$orderNoTrees = $this->genOrderNoTrees($this->getNotYetModifyed());

		$this->genUpdateCCSOrderIndexQuerysByIterateOrderNosAndExecute($orderNoTrees)->updateSellIndex();

		return $this->setModifyOrders($orderNoTrees);
	}

	/**
	 * 修改銷貨單 PREFIX 為 CT，
	 * 因為銷貨單(PIS_SellIndex)和訂單(CCS_OrderInde) 有建立的時間差，
	 * 因此需要另外拉一個 method 處理。
	 * 
	 * @return $this
	 */
	protected function updateSellIndex()
	{
		$condition = self::COMETRUES_PREFIX . '%';
		$dateTime = with(new \DateTime())->modify('-2 days')->format('Y-m-d H:i:s');

		$ctOrderNos = Processor::getArrayResult("SELECT " . self::ORDERNO_KEY . ", MustPayTotal FROM CCS_OrderIndex WHERE " . self::ORDERNO_KEY . " LIKE '{$condition}' AND CRT_TIME >= '{$dateTime}'");

		foreach ($ctOrderNos as $ctOrderNo) {
			$orderNoWithCT = array_get($ctOrderNo, self::ORDERNO_KEY);
			
			$orderNoWithoutCT = str_replace('T', '', $orderNoWithCT);

			$payTotal = array_get($ctOrderNo, 'MustPayTotal');

			Processor::execErp("UPDATE PIS_SellIndex SET No='{$orderNoWithCT}' WHERE No='{$orderNoWithoutCT}' AND TaxedTotal={$payTotal}");
		}

		return $this;
	}

	protected function genOrderNoTrees(array $orderDivs)
	{
		$orders = [];

		foreach ($orderDivs as $orderDiv) {
			$indexSerNo = array_get($orderDiv, self::SERNO_KEY);

			if (!array_key_exists($indexSerNo, $orders)) {
				$orders[array_get($orderDiv, self::SERNO_KEY)] = [];
			}

			$orders[array_get($orderDiv, self::SERNO_KEY)][] = $orderDiv;
		}

		return $this->createTrees($orders);
	}

	protected function createTrees(array $orders)
	{
		$trees = [];

		foreach ($orders as $serNo => $orderDiv) {
			$trees[] = new OrderNoTree($serNo, array_get($orderDiv, '0.' . self::ORDERNO_KEY), array_pluck($orderDiv, self::DIVNO_KEY));
		}

		return $trees;
	}

	public function getModifyOrders()
	{
		return $this->modifyOrders;
	}

	public function setModifyOrders(array $modifyOrders)
	{
		$this->modifyOrders = $modifyOrders;

		return $this;
	}

	public function addModifyOrders($key, $value)
	{
		$this->modifyOrders[$key] = $value;

		return $this;
	}

	public function getNotYetModifyed()
	{
		return Processor::getArrayResult($this->getNotYetModifyedQuery());
	}

	/**
	 * getNotYetModifyedTodayQuery
	 * 
	 * @return Illuminate\Database\Query\Builder
	 */
	protected function getNotYetModifyedQuery()
	{
		return Processor::table('CCS_OrderDivIndex')
			->leftJoin('CCS_OrderIndex', 'CCS_OrderIndex.SerNo', '=', 'CCS_OrderDivIndex.IndexSerNo')
			->leftJoin('FAS_Corp', 'CCS_OrderIndex.DeptSerNo', '=', 'FAS_Corp.SerNo')
			->select('CCS_OrderIndex.SerNo, CCS_OrderIndex.OrderNo', 'CCS_OrderDivIndex.No')
			->where('CCS_OrderIndex.OrderNo', 'NOT LIKE', 'CT%')	
			->where($this->corpConditionCallback())
		;
	}

	protected function corpConditionCallback()
	{
		return function ($q) {
				$q
					->where('FAS_Corp.Code', 'LIKE', 'K%')
					->orWhere('FAS_Corp.Code', 'LIKE', 'CT%')
				;
			};
	}

	/**
	 * genUpdateCCSOrderIndexQuerysByIterateOrderNos
	 * 
	 * @param  array  $orders
	 * @return array        
	 */
	public function genUpdateCCSOrderIndexQuerysByIterateOrderNosAndExecute(array &$orderNoTrees)
	{
		foreach ($orderNoTrees as $orderNoTree) {
			$orderNo = $orderNoTree->getFirstName();
			$newOrderNo = $this->getNewInsertCTOrderNo($this->getConvertOrderNo($orderNo));

			Processor::execErp("UPDATE CCS_OrderIndex SET OrderNo='{$newOrderNo}' WHERE OrderNo='{$orderNo}'");
			
			foreach ($orderNoTree->getChildren() as $child) {
				$newNo = $newOrderNo . $orderNoTree->fetchTailOfChild($child);

				Processor::execErp("UPDATE CCS_OrderDivIndex SET No='{$newNo}' WHERE No='{$child}'");
			}

			$orderNoTree->setFirstName($newOrderNo);			
		}

		return $this;
	}

	protected function getConvertOrderNo($orderNo)
	{
		$orderNo = str_replace(['C', 'T'], ['', ''], $orderNo);

		return self::COMETRUES_PREFIX . $orderNo;
	}

	protected function getNewInsertCTOrderNo($ctOrderNo)
	{
		return ($this->isCTOrderNoExist($ctOrderNo)) ? $this->getNewInsertCTOrderNo($this->getNextCTOrderNo($ctOrderNo)) : $ctOrderNo;
	}

	protected function isCTOrderNoExist($ctOrderNo)
	{
		$res = Processor::getArrayResult("SELECT * FROM CCS_OrderIndex WHERE OrderNo='{$ctOrderNo}'");

		return !empty($res);
	}

	protected function getNextCTOrderNo($ctOrderNo)
	{
		$res = Processor::getArrayResult("SELECT TOP 1 * FROM CCS_OrderIndex WHERE OrderNo='{$ctOrderNo}' ORDER BY SerNo DESC");

		$numPart = substr(array_get($res, '0.OrderNo'), 2);

		return self::COMETRUES_PREFIX . (++ $numPart);
	}
}
