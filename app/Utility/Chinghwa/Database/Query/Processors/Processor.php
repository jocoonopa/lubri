<?php

namespace App\Utility\Chinghwa\Database\Query\Processors;

use App\Utility\Chinghwa\Database\Connectors\Connector;
use Illuminate\Database\Query\Builder;

class Processor
{
    public static function table($name)
    {
        return app()->make('db')->table($name);
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

    public static function getErp(Builder $queryBuilder)
    {
        return self::execErp(self::toSql($queryBuilder));
    }

    public static function getPos(Builder $queryBuilder)
    {
        return self::execPos(self::toSql($queryBuilder));
    }

    public static function getCti(Builder $queryBuilder)
    {
        return self::execCti(self::toSql($queryBuilder));
    }

    /**
     * Execute query and fetch result then stored in array($src)
     * 
     * @param  string|object    $query 
     * @param  closure          $callback 
     * @example  function (&$insertRows, $row) {
     *     $insertRows[] = $row;
     * };
     * @param  array &$src  
     *   
     * @return void         
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
     * Execute query and return array result
     * 
     * @param  string|object $query 
     * @param  string $dbFlag
     * @return array        
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

    /**
     * Return sql statement
     * 
     * @param  Builder $queryBuilder 
     * @return string                
     */
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

    public static function getStorageSql($filePath)
    {
        return file_get_contents(__DIR__ . '/../../../../../../storage/sql/' . $filePath);
    }
}