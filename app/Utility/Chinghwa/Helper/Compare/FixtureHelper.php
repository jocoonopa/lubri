<?php

namespace App\Utility\Chinghwa\Helper\Compare;

class FixtureHelper
{
	/**
     * 取得會員區別代碼
     *
     * 這邊作法很蠢我知道，不過至少可以坦個十個月
     * 這cp值還可以接受拉
     *
     * 126-67  126-67寵兒俱樂部-104-07 
     * 
     * @param  string $date [yyyymm]
     * @return string       
     */
    public static function getDistincCode($date)
    {
        $map = [
            '201508' => '126-68',
            '201509' => '126-69',
            '201510' => '126-70',
            '201511' => '126-71',
            '201512' => '126-72',
            '201601' => '126-73',
            '201602' => '126-74',
            '201603' => '126-75',
            '201604' => '126-76',
            '201605' => '126-77'
        ];

        return (array_key_exists($date, $map)) ? $map[$date] : 'DISTINC_UNDEFINED';
    }

	/**
	 * 會員資料容器，包含新增檔案資料和更新檔案資料以及chunk統計
	 * 
	 * @var array
	 */
    public static function getDataPrototype($realPath)
    {
        return [
            'insert'             => [], 
            'update'             => [], 
            'iterateInsertTimes' => 0,
            'iterateUpdateTimes' => 0,
            'realpath'           => $realPath
        ];
    }    
}