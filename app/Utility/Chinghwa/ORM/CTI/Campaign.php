<?php

namespace App\Utility\Chinghwa\ORM\CTI;

use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\ORM\iORM;
use Carbon\Carbon;

class Campaign implements iORM
{
    public static function isExist(array $options){}

    public static function first(array $options){}

    public static function find(array $options){}

    public static function findValid()
    {
        return Processor::getArrayResult(Processor::table('Campaign')
            ->select('*')
            ->where('Campaign.Enabled', '=', 1)
            ->where('Campaign.StartDate', '<=', Carbon::now()->format('Ymd'))
            ->where('Campaign.EndDate', '>=', Carbon::now()->format('Ymd')),
            'Cti'
        );
    }
}