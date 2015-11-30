<?php

namespace App\Utility\Chinghwa\Helper\Flap;

use App\User;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;

class PosMemberListHelper
{
	public function get(User $user)
	{
		return Processor::getArrayResult($this->getQb($user));
	}

	protected function getQb(User $user)
	{
		return  Processor::table('Customer_lubri')
			->where('emp_id', strval($user->code))
			->where('distflags_7', 'Y')
			->where('cust_status', '1')
			->select(implode(',', $this->getColumns()))
		;
	}

	protected function getColumns()
	{
		return ['cust_id', 'cust_mobilphone', 'cust_email', 'cust_birthday', 'cust_tel1', 'cust_tel2', 'cust_bonus', 'cust_totalconsume', 'ob_firstbuy', 'Cust_traxdate', 'ob_memo', 'cust_memo', 'fn_memo', 'cust_sex', 'cust_cname'];
	}

	protected function getInsertFunc()
    {
        return function (&$insertRows, $row) {
            $insertRows[] = $row;
        };
    }

	public function buildCache()
	{

	}

	public function getExpireCache()
	{

	}

	public function getFromServer()
	{

	}

	public function getFromCache()
	{

	}

	public function refresh()
	{

	}
}