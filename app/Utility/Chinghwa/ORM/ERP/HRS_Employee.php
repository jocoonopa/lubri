<?php

namespace App\Utility\Chinghwa\ORM\ERP;

use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\ORM\iORM;

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

    protected static function getBase(array $options)
    {
        return Processor::table('HRS_Employee')            
            ->where('Code', '=', array_get($options, 'code'))
        ;
    }
}