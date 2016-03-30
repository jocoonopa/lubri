<?php

namespace App\Utility\Chinghwa\Database\Connectors;

class Connector
{
    protected static $erpCnx = NULL;
    protected static $posCnx = NULL;
    protected static $ctiCnx = NULL;

	public static function toErp()
    {
        if (!(self::$erpCnx = odbc_connect(env('ODBC_ERP_DSN'), env('ODBC_ERP_USER'), env('ODBC_ERP_PWD'), SQL_CUR_USE_ODBC))) {
            throw new \Exception('odbc error');
        }

        return self::$erpCnx;
    }

    public static function toPos()
    {
        if (!(self::$posCnx = odbc_connect(env('ODBC_POS_DSN'), env('ODBC_POS_USER'), env('ODBC_POS_PWD'), SQL_CUR_USE_ODBC))) {
            throw new \Exception('odbc error');
        }

        return self::$posCnx;
    }

    public static function toCti()
    {
        if (!(self::$ctiCnx = odbc_connect(env('ODBC_CTI_DSN'), env('ODBC_CTI_USER'), env('ODBC_CTI_PWD'), SQL_CUR_USE_ODBC))) {
            throw new \Exception('odbc error');
        }

        return self::$ctiCnx;
    }
}