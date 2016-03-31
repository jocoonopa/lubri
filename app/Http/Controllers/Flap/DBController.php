<?php

namespace App\Http\Controllers\Flap;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Utility\Chinghwa\Database\Connectors\Connector;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Illuminate\Http\Request;

class DBController extends Controller
{
    protected $cnx = NULL;

    public function testsp()
    {
        $codes = ['A00021','A00034','A00047','A00100','A00286','A00267','A00422','A00438','A00458','A00459','A00460','A00461','A00463','A00473','A00474','A00482','A00486','A00490','A00491','A00492','A00493','A00495','A00497','A00499','A00500','A00506','A00513','A00519','A00520','A00537','A00539','A00540','A00541','A00542'];

        for ($i = 1; $i <= 12; $i ++) {
            $date = new \DateTime("2015-$i-01");
            $startString = $date->format('Ymd');
            $endString = $date->modify('last day of this month')->format('Ymd');

            $sql = str_replace(['$startString', '$endString', '$codes'], [$startString, $endString, implode($codes, "','")], file_get_contents(__DIR__ . '/2015_salerecord_by_pcode.sql'));

            echo $sql . "<hr>";
        }

        $sql = str_replace(['$startString', '$endString', '$codes'], ['20150101', '20151231', implode($codes, "','")], file_get_contents(__DIR__ . '/2015_qty_by_pcode.sql'));

         echo $sql . "<hr>";
        // $res = Processor::getArrayResult(file_get_contents(__DIR__ . '/test.sql'));

        // pr($res);
    }

    public function find()
    {
        set_time_limit(0);

        $this->cnx = Connector::toErp();

        $startTime = microtime(true);
        
        $this->process();

        $endTime = microtime(true);

        return '<hr />cost ' . floor($endTime - $startTime) . ' seconds';
    }

    protected function process()
    {
        $columns = Processor::getArrayResult($this->getQuery());

        echo 'From ' . count($columns) . 'Columns,' . "<br /><br />";

        foreach ($columns as $column) {
            $this->compareDisplay($column['COLUMN_NAME'], $column['TABLE_NAME']);
        }
    }

    /**
     * Memory leak caused by Sys_DMTaskInfo.maintain, it contains strange sp...
     */
    protected function compareDisplay($columnName, $tableName)
    {
        $c = Processor::getArrayResult("SELECT TOP 1 [{$columnName}] FROM {$tableName} WITH(NOLOCK)");
        $serNo = array_get($c, "0.{$columnName}", null);

        if ($this->isTarget($columnName, $serNo)) {
            echo '記憶體使用:' . memory_get_usage() . ':';
            echo $serNo . ':';
            echo $columnName . ':';
            echo $tableName . "<br />";
        }
    }

    protected function getTargetList()
    {
        return [
            'MDT_TIME'
        ];
    }

    protected function isTarget($columnName, $serNo)
    {
        // foreach ($this->getTargetList() as $targetString) {
        //     if ($targetString === $columnName && substr($serNo, 0, 7) >= '2015-08') {
        //         return true;
        //     }
        // }
        // 
        // return false;
        
        return 'RR' === substr($serNo, 0, 2);
    }

    protected function getQuery()
    {
        return Processor::table('INFORMATION_SCHEMA.COLUMNS')
            ->select('INFORMATION_SCHEMA.COLUMNS.COLUMN_NAME, INFORMATION_SCHEMA.COLUMNS.TABLE_NAME')
            ->leftJoin('information_schema.tables', 'information_schema.tables.TABLE_NAME', '=', 'INFORMATION_SCHEMA.COLUMNS.TABLE_NAME')
            ->where('INFORMATION_SCHEMA.COLUMNS.ordinal_position', '<=', 20)
            ->where('INFORMATION_SCHEMA.COLUMNS.COLUMN_NAME', 'NOT LIKE', '%time%')
            ->whereNotIn('INFORMATION_SCHEMA.COLUMNS.COLUMN_NAME', ['uid', 'Key', 'Open'])
            ->where('INFORMATION_SCHEMA.tables.TABLE_TYPE', '=', 'BASE TABLE')
            ->where('INFORMATION_SCHEMA.tables.TABLE_NAME', 'NOT LIKE', '%BAK_%')
            ->where('INFORMATION_SCHEMA.tables.TABLE_NAME', 'NOT LIKE', '%_BAK%')
            ->where('INFORMATION_SCHEMA.tables.TABLE_NAME', 'NOT LIKE', 'Z_%')
            ->where('INFORMATION_SCHEMA.tables.TABLE_NAME', 'NOT LIKE', 'SYS%')
            //->where('INFORMATION_SCHEMA.tables.TABLE_NAME', 'LIKE', 'FAS%')
            ->orderBy('INFORMATION_SCHEMA.COLUMNS.TABLE_NAME')
        ;
    }
}
