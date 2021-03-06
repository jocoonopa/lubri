<?php

namespace App\Utility\Chinghwa\Database\Query\Processors;

use App\Utility\Chinghwa\Database\Connectors\Connector;
use Illuminate\Database\Query\Builder;

class Processor
{
    const DB_ERP = 'Erp';
    const DB_POS = 'Pos';
    const DB_CTI = 'Cti';

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
        return self::execSpecific(Connector::toErp(), $query);
    }

    public static function execPos($query)
    {
        return self::execSpecific(Connector::toPos(), $query);
    }

    public static function execCti($query)
    {
        return self::execSpecific(Connector::toCti(), $query);
    }

    public static function execSpecific($cnx, $query)
    {        
        $res = odbc_exec($cnx, cb5($query));

        return $res;
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
    public static function getArrayResult($query, $dbFlag = self::DB_ERP)
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

        $execDB = NULL;
        $res = NULL;
        $row = NULL;
        
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

    public static function getStorageSql($fileName)
    {
        return file_get_contents(__DIR__ . '/../../../../../../storage/sql/' . $fileName);
    }

    public static function getWrapVal($val)
    {
        $val = str_replace("'", '', $val);
        
        return empty($val) ? 'NULL' : "'{$val}'";
    }
}