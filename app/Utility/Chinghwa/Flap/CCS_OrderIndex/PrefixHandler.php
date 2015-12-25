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
		$orders = $this->getNotYetModifyed();

		$orderNos = array_pluck($orders, self::ORDERNO_KEY);

		$indexSerNos = array_pluck($this->getCCSOrderDivIndexNotYetModifyed(array_pluck($orders, self::SERNO_KEY)), self::DIVNO_KEY);

		$this
			->execWithQueryArray($this->genUpdateCCSOrderDivIndexQuery($indexSerNos))
			->execWithQueryArray($this->genUpdateCCSOrderIndexQuerysByIterateOrderNos($orderNos))
		;

		return $this->setModifyOrders($orderNos);
	}

	protected function execWithQueryArray(array $qs)
	{
		foreach ($qs as $query) {
			Processor::execErp($query);
		}

		return $this;
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

	public function getNotYetModifyed()
	{
		return Processor::getArrayResult($this->getNotYetModifyedQuery());
	}

	protected function getCCSOrderDivIndexNotYetModifyed(array $serNos)
	{
		return Processor::getArrayResult($this->getCCSOrderDivIndexNotYetModifyedQuery($serNos));
	}

	/**
	 * getNotYetModifyedTodayQuery
	 * 
	 * @return Illuminate\Database\Query\Builder
	 */
	protected function getNotYetModifyedQuery()
	{
		return Processor::table('CCS_OrderIndex')
			->leftJoin('FAS_Corp', 'CCS_OrderIndex.DeptSerNo', '=', 'FAS_Corp.SerNo')
			->select('CCS_OrderIndex.SerNo, CCS_OrderIndex.OrderNo')
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

	protected function getCCSOrderDivIndexNotYetModifyedQuery(array $serNos)
	{
		return Processor::table('CCS_OrderDivIndex')
			->whereIn('IndexSerNo', $serNos)	
		;
	}

	public function genUpdateCCSOrderDivIndexQuery(array $nos)
	{
		$qs = [];

		foreach ($nos as $no) {
			$qs[] = "UPDATE CCS_OrderDivIndex SET No='{$this->getConvertOrderNo($no)}' WHERE No='{$no}'";
		} 

		return $qs;
	}

	/**
	 * genUpdateCCSOrderIndexQuerysByIterateOrderNos
	 * 
	 * @param  array  $orderNos
	 * @return array        
	 */
	public function genUpdateCCSOrderIndexQuerysByIterateOrderNos(array $orderNos)
	{
		$qs = [];

		foreach ($orderNos as $orderNo) {
			$qs[] = "UPDATE CCS_OrderIndex SET OrderNo='{$this->getConvertOrderNo($orderNo)}' WHERE OrderNo='{$orderNo}'";
		}

		return $qs;
	}

	protected function getConvertOrderNo($orderNo)
	{
		$orderNo = str_replace(['C', 'T'], ['', ''], $orderNo);

		return self::COMETRUES_PREFIX . $orderNo;
	}
}
