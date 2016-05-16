<?php

namespace App\Utility\Chinghwa\Helper\Flap\PIS_Goods\FixCPrefixGoods;

use App\Model\User;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Carbon\Carbon;

class DataHelper
{
	const C_COLOR_SERNO = 'COLOR000000000000000003159';

	/**
	 * 根據傳入的SerNo 集合而成的陣列，取得 PIS_Goods 的集合陣列
	 * 
	 * @param  array  $serNos [array of serno]
	 * @return array
	 */
	public function fetchGoodsesBySerNos(array $serNos)
	{
		return Processor::getArrayResult($this->getFetchGoodsesBySerNosQuery($serNos));
	}

	protected function getFetchGoodsesBySerNosQuery(array $serNos)
	{
		return Processor::table('PIS_Goods')
			->whereIn('SerNo', $serNos)
		;
	}

	/**
	 * 根據傳入的code 集合而成的陣列，取得 PIS_Goods 的集合陣列
	 * 
	 * @param  array  $codes
	 * @return array
	 */
	public function fetchGoodsesByCodes(array $codes)
	{
		return Processor::getArrayResult($this->getFetchGoodsesByCodesQuery($codes));
	}

	protected function getFetchGoodsesByCodesQuery(array $codes)
	{
		return Processor::table('PIS_Goods')
			->whereIn('Code', $codes)
		;
	}

	/**
	 * 取得 $number 日之內建立的商品清單[ PIS_Goods 的集合陣列 ]
	 * 
	 * @param  int $number [幾天之內的天數]
	 * @return array
	 */
	public function getNDaysBeforeCreatedCodes($number)
	{
		return Processor::getArrayResult($this->getNDaysBeforeCreatedCodesQuery($number));
	}

	protected function getNDaysBeforeCreatedCodesQuery($number)
	{
		return Processor::table('PIS_Goods')
			->where('CRT_TIME', '>=', with(new Carbon)->modify("- {$number} days")->format('Y-m-d H:i:s'))
			->where('Code', 'NOT LIKE', 'C%')
			->orderBy('CRT_TIME', 'DESC')
		;
	}

	/**
	 * 根據 getNDaysBeforeCreatedCodes() 的結果，
	 * 判斷傳入的 $codes 有無違法修改(mass assign)的意圖
	 * 
	 * @param  array  $codes  [PIS_Goods 的 code 集合陣列]
	 * @param  int 	  $number [幾天之內的天數]
	 * @return array
	 */
	public function getMassAssign(array $codes, $number)
	{	
		$nDaysCodes = array_fetch($this->getNDaysBeforeCreatedCodes($number), 'Code');

		return array_diff($codes, $nDaysCodes);
	}

	/**
	 * 轉將指定的PIS_Goods(by Code)，轉換為贈品
	 * 
	 * @param  array  $codes      [PIS_Goods 的 code 集合陣列]
	 * @param  int 	  $beforeDays [幾天之內的天數]
	 * @return array  $map 		  ["originCode" => 'newCode']            
	 */
	public function convertToCGoods(array $codes, $beforeDays)
	{
		$map = [];

		foreach ($codes as $code) {
			Processor::execErp($this->getConvertyQuery($newCCode = $this->getNewCCode(), $code));

			$map[$code] = $newCCode;
		}

		return $map;
	}

	protected function getConvertyQuery($newCCode, $code)
	{
		return "UPDATE PIS_Goods SET Code='{$newCCode}', GoodsStyleCode='{$newCCode}', Barcode='{$newCCode}', ColorSerNo='{$this->getCColorSerNo()}' WHERE Code='{$code}'";
	}

	/**
	 * 取得新的可插入之 Code
	 * 
	 * @return string        
	 */
	protected function getNewCCode()
	{
		$latestCcode = array_get($this->findLatestCCode(), '0.Code');

		$incrementNum = preg_replace('/\D/', '', $latestCcode);

		return substr($latestCcode, 0, 1) . str_pad(++ $incrementNum, 5, 0, STR_PAD_LEFT);
	}

	protected function findLatestCCode()
	{
		return Processor::getArrayResult($this->getFindLatestCCodeQuery());
	}

	protected function getFindLatestCCodeQuery()
	{
		return Processor::table('PIS_Goods')
			->select('TOP 1 Code')
			->where('Code', 'LIKE', 'C%')
			->where('Code', 'NOT LIKE', 'CT%')
			->where('Code', '<>', 'C1470')
			->orderBy('Code', 'DESC')
		;
	}

	public function getCColorSerNo()
	{
		return self::C_COLOR_SERNO;
	}
}