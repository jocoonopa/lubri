<?php

namespace App\Utility\Chinghwa\Helper\Flap;

use App\User;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use DB;

class PosMemberProfileHelper
{
	public function get(User $user)
	{
		$list = [];

		$qb = str_replace('$empserno', $user->serno, $this->getQueryProtoType());

		Processor::fetchArray($qb, $this->getInsertFunc(), $list);

		return $list;
	}

	protected function getQueryProtoType()
	{
		return file_get_contents(__DIR__ . '/../../../../storage/sql/member/profile.sql');
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