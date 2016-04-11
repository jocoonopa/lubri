<?php

namespace App\Utility\Chinghwa\Flap\ORM;

use App\Utility\Chinghwa\Database\Query\Processors\Processor;

class BasicDataDef implements iORM 
{
    public static function isExist(array $options)
    {
        return 0 < array_get(Processor::getArrayResult($this->getBase($options)->select('count(*) AS count')), '0.count', 0);
    }

    public static function first(array $options)
    {
        return array_get(Processor::getArrayResult($this->getBase($options)->select('TOP 1 *')), '0');
    }

    public static function find(array $options)
    {
        return Processor::getArrayResult($this->getBase($options)->select('*'));
    }

    public static function getBase(array $options)
    {
        return Processor::table('BasicDataDef')->where(function ($q) use ($options) {
            foreach ($options as $column => $value) {
                $q->where($column, '=', $value);
            }
        });        
    }
}