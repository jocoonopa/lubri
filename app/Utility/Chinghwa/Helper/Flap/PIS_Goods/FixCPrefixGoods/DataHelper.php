<?php

namespace App\Utility\Chinghwa\Helper\Flap\PIS_Goods\FixCPrefixGoods;

use App\Model\User;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Carbon\Carbon;

class DataHelper
{
	const C_COLOR_SERNO = 'COLOR000000000000000003159';

	public function fetchGoodsesBySerNos(array $serNos)
	{
		return Processor::getArrayResult($this->getFetchGoodsesBySerNos($serNos));
	}

	protected function getFetchGoodsesBySerNos(array $serNos)
	{
		return Processor::table('PIS_Goods')
			->whereIn('SerNo', $serNos)
		;
	}

	protected function getFetchGoodsesByCodes(array $codes)
	{
		return Processor::table('PIS_Goods')
			->whereIn('Code', $codes)
		;
	}

	public function fetchGoodsesByCodes(array $codes)
	{
		return Processor::getArrayResult($this->getFetchGoodsesByCodes($codes));
	}

	public function getNDaysBeforeCreatedCodes($number)
	{
		return Processor::getArrayResult($this->getNDaysBeforeCreatedCodesQuery($number));
	}

	protected function getNDaysBeforeCreatedCodesQuery($number)
	{
		return Processor::table('PIS_Goods')
			->where('CRT_TIME', '>=', with(new Carbon)->modify("- {$number} days")->format('Y-m-d H:i:s'))
			->where('Code', 'NOT LIKE', 'C%')
		;
	}

	public function getMassAssign(array $codes, $number)
	{	
		$nDaysCodes = array_fetch($this->getNDaysBeforeCreatedCodes($number), 'Code');

		return array_diff($codes, $nDaysCodes);
	}

	public function convertToCGoods(array $codes, $beforeDays)
	{
		foreach ($codes as $code) {
			$newCCode = $this->getNewCCode($beforeDays);

			$query = "UPDATE PIS_Goods SET Code='{$newCCode}', Barcode='{$newCCode}', ColorSerNo='{$this->getCColorSerNo()}' WHERE Code='{$code}'";

			Processor::execErp($query);
		}

		return $this;
	}

	protected function getNewCCode($number)
	{
		$latestCcode = array_get($this->findLatestCCode($number), '0.Code');

		$incrementNum = preg_replace('/\D/', '', $latestCcode);

		return substr($latestCcode, 0, 1) . str_pad(++ $incrementNum, 5, 0, STR_PAD_LEFT);
	}

	protected function findLatestCCode($number)
	{
		return Processor::getArrayResult($this->getFindLatestCCodeQuery($number));
	}

	protected function getFindLatestCCodeQuery($number)
	{
		return Processor::table('PIS_Goods')
			->select('TOP 1 Code')
			->where('Code', 'LIKE', 'C%')
			->where('Code', 'NOT LIKE', 'CT%')
			->orderBy('Code', 'DESC')
		;
	}

	public function getCColorSerNo()
	{
		return self::C_COLOR_SERNO;
	}
}