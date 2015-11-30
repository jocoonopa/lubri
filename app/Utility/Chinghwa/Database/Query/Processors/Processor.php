<?php

namespace App\Utility\Chinghwa\Database\Query\Processors;

use App\Utility\Chinghwa\Database\Connectors\Connector;
use Illuminate\Database\Query\Builder;
use DB;

class Processor
{
    public static function table($name)
    {
        return DB::table($name);
    } 

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

    /**
     * [fetchArray description]
     * @param  [type] $query    [description]
     * @param  [type] $callback 
     * @example  function (&$insertRows, $row) {
     *     $insertRows[] = $row;
     * };
     * @param  [type] &$src     [description]
     * @return [type]           [description]
     */
    public static function fetchArray($query, $callback, &$src)
    {
        if ($query instanceof Builder) {
            $query = self::toSql($query);
        }

        if ($res = self::execErp($query)) {
            while ($row = odbc_fetch_array($res)) {
                c8res($row);
                $callback($src, $row);
            }
        }
    }

    /**
     * [fetchArray description]
     * @param  [type] $query    [description]
     * @param  [type] $callback 
     * @example  function (&$insertRows, $row) {
     *     $insertRows[] = $row;
     * };
     * @param  [type] &$src     [description]
     * @return [type]           [description]
     */
    public static function getArrayResult($query, $dbFlag = 'Erp')
    {
        $data = [];

        if ($query instanceof Builder) {
            $query = self::toSql($query);
        }

        $execDB = "exec{$dbFlag}";

        if ($res = self::$execDB($query)) {
            while ($row = odbc_fetch_array($res)) {
                c8res($row);
                $data[] = $row;
            }
        }

        return $data;
    }

    public static function toSql(Builder $queryBuilder)
    {
        $params = $queryBuilder->getBindings();
        $pdoStatement = $queryBuilder->toSql();

        foreach ($params as $param) {
            $param = (is_int($param)) ? $param : "'{$param}'";

            $pdoStatement = str_replace_first('?', $param, $pdoStatement);
        }

        return str_replace('`', '', $pdoStatement);
    }

    public static function getErp(Builder $queryBuilder)
    {
        return self::execErp(self::toSql($queryBuilder));
    }
}