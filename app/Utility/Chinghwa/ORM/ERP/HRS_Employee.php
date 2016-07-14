<?php

namespace App\Utility\Chinghwa\ORM\ERP;

use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\ORM\iORM;
use DB;

class HRS_Employee implements iORM
{
    public static function isExist(array $options)
    {
        return 0 < array_get(Processor::getArrayResult(self::getBase($options)->select('count(*) AS count')), '0.count', 0);
    }

    public static function first(array $options)
    {
        return array_get(Processor::getArrayResult(self::getBase($options)->select('TOP 1 *')), '0', NULL);
    }

    public static function find(array $options)
    {
        return Processor::getArrayResult(self::getBase($options)->select('*'));
    }

    public static function findByCorps(array $corps)
    {
        $q = Processor::table('HRS_Employee')
            ->select(DB::raw('HRS_Employee.Code, HRS_Employee.Name, FAS_Corp.Code AS FCode, FAS_Corp.Name AS FName'))
            ->leftJoin('FAS_Corp', 'HRS_Employee.CorpSerNo', '=', 'FAS_Corp.SerNo')
            ->whereIn('FAS_Corp.Code', $corps)
            ->where('HRS_Employee.Leave', '=', 0)
            ->orderBy('FAS_Corp.Code', 'ASC')
            ->orderBy('HRS_Employee.Code', 'ASC')
        ;

        return Processor::getArrayResult($q);
    }

    protected static function getBase(array $options)
    {
        return Processor::table('HRS_Employee')            
            ->where('Code', '=', array_get($options, 'code'))
            ->where('Leave', '=', 0)
        ;
    }
}