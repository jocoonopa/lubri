<?php

namespace App\Utility\Chinghwa\ORM\ERP;

use App\Import\Flap\POS_Member\Import;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\ORM\iORM;

class POS_Member implements iORM
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
        return Processor::table('Customer_lubri')            
            ->where('cust_cname', '=', array_get($options, 'name', NULL))
            ->where('member_id', 'NOT LIKE', 'CT%')
            ->where(self::getOr($options))
            ->orderBy('member_id', 'DESC')
        ;
    }

    protected static function getOr(array $options)
    {
        return function ($q) use ($options) {
            $q->orWhere(function($q) use ($options) {
                $q->where('LEN(cust_mobilphone)', '>', Import::MINLENGTH_CELLPHONE)->where('cust_mobilphone', '=', array_get($options, 'cellphone', '########'));
            })->orWhere(function($q) use ($options) {
                $q->where('LEN(cust_tel1)', '>', Import::MINLENGTH_TEL)->where('cust_tel1', '=', array_get($options, 'hometel', '########'));
            })->orWhere(function($q) use ($options) {
                $q->where('LEN(cust_addconn)', '>', Import::MINLENGTH_ADDRESS)->where('cust_addconn', '=', array_get($options, 'address', '########'));
            });
        };
    }
}