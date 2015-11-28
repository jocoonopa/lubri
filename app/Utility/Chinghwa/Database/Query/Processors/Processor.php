<?php

namespace App\Utility\Chinghwa\Database\Query\Processors;

use App\Utility\Chinghwa\Database\Connectors\Connector;

class Processor
{
	public static function execute($query, $cnx)
    {
        return odbc_exec($cnx, cb5($query));
    }

    public static function execErp($query)
    {
        return odbc_exec(Connector::toErp(), cb5($query));
    }

    public static function execPos($query)
    {
        return odbc_exec(Connector::toPos(), cb5($query));
    }

    public static function execCti($query)
    {
        return odbc_exec(Connector::toCti(), cb5($query));
    }

    public static function fetchArray($query, $callback, &$src)
    {
        if ($res = $this->execErp($query)) {
            while ($row = odbc_fetch_array($res)) {
                c8res($row);
                $callback($src, $row);
            }
        }
    }
}