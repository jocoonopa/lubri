<?php

namespace App\Utility\Chinghwa\Helper\Compare\Handler;

use App\Utility\Chinghwa\Compare\HoneyBaby;
use App\Utility\Chinghwa\Database\Query\Grammers\Grammer;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;

class ExistHandler
{
    protected static function getNameList($result)
    {
    	$nameList = []; 

    	foreach ($result as $row) {
            $nameList[] = getRowVal($row, HoneyBaby::IMPORT_NAME_INDEX);
        }

        return $nameList; 
    }

    public static function fetchMightExistMembers($result)
    {
    	$mightExistMembers = [];

    	$nameQuery = self::getNameQuery(self::getNameList($result));

    	if ($res = Processor::execErp($nameQuery)) {
           while ($mightExistMembers[] = odbc_fetch_array($res));
        }

        return $mightExistMembers;
    }

   	protected static function getNameQuery(array $names)
    {
        $inString = Grammer::genInQuery($names);

        $sql = "SELECT Code,Name,HomeTel,OfficeTel,CellPhone,HomeAddress_State,HomeAddress_City,HomeAddress_Address FROM POS_Member WHERE Name IN ({$inString})";

        return $sql;
    }

    /**
     * 1. 姓名 + 手機 且 手機不為空
     * 2. 姓名 + 住址 且 住址不為空
     * 3. 姓名 + 家裡電話 且 家裡電話不為空
     * 
     * @param array       $mightExistMembers [資料庫存在的會員資料]
     * @param collection  $row          [EXCEL 資料]
     */
    public static function isExist($mightExistMembers, $row)
    {
        foreach ($mightExistMembers as $key => $exitstMember) {
            if (trim(c8($exitstMember['Name'])) !== trim($row[HoneyBaby::IMPORT_NAME_INDEX])) {
                continue;
            }

            if (self::strictCompare(c8($exitstMember['CellPhone']), $row[HoneyBaby::IMPORT_MOBILE_INDEX])) {
                return $exitstMember;
            }

            if (self::strictCompare(c8($exitstMember['HomeAddress_Address']), $row[HoneyBaby::IMPORT_ADDRESS_INDEX])) {
                return $exitstMember;
            }

            if (self::strictCompare(c8($exitstMember['HomeTel']), $row[HoneyBaby::IMPORT_HOMETEL_INDEX])) {
                return $exitstMember;
            }
        }

        return false;
    }

    /**
     * 將多餘自元過濾後進行比對，且第一個字串不得為空，
     * 符合以上兩個條件才會傳回true
     * 
     * @param  string $str1       
     * @param  string $str2       
     * @param  array  $placeholder
     * @return boolean
     */
    protected static function strictCompare($str1, $str2, $placeholder = [''])
    {
        return !empty(str_replace(getReplaceWords(), $placeholder, $str1)) && str_replace(getReplaceWords(), $placeholder, $str1) === str_replace(getReplaceWords(), $placeholder, $str2);
    }
}