<?php

namespace App\Http\Controllers\Flap;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;

class DBController extends Controller
{
    public function find()
    {
        set_time_limit(0);
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

    protected function compareDisplay($columnName, $tableName)
    {
        $c = Processor::getArrayResult("SELECT TOP 1 {$columnName} FROM {$tableName} WITH(NOLOCK)");

        $serNo = array_get($c, '0.' . $columnName, null);

        if ($this->isTarget($columnName, $serNo)) {
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
        foreach ($this->getTargetList() as $targetString) {
            if ($targetString === $columnName && substr($serNo, 0, 7) >= '2015-08') {
                return true;
            }
        }

        return false;
    }

    protected function getQuery()
    {
        return Processor::table('INFORMATION_SCHEMA.COLUMNS')
            ->select('INFORMATION_SCHEMA.COLUMNS.COLUMN_NAME, INFORMATION_SCHEMA.COLUMNS.TABLE_NAME')
            ->leftJoin('information_schema.tables', 'information_schema.tables.TABLE_NAME', '=', 'INFORMATION_SCHEMA.COLUMNS.TABLE_NAME')
            //->where('INFORMATION_SCHEMA.COLUMNS.ordinal_position', '<=', 20)
            //->where('INFORMATION_SCHEMA.COLUMNS.COLUMN_NAME', 'NOT LIKE', '%time%')
            ->whereNotIn('INFORMATION_SCHEMA.COLUMNS.COLUMN_NAME', ['uid', 'Key', 'Open'])
            ->where('INFORMATION_SCHEMA.tables.TABLE_TYPE', '=', 'BASE TABLE')
            ->where('INFORMATION_SCHEMA.tables.TABLE_NAME', 'NOT LIKE', '%BAK_%')
            ->where('INFORMATION_SCHEMA.tables.TABLE_NAME', 'NOT LIKE', '%_BAK%')
            ->where('INFORMATION_SCHEMA.tables.TABLE_NAME', 'NOT LIKE', 'Z_%')
            ->orderBy('INFORMATION_SCHEMA.COLUMNS.TABLE_NAME')
        ;
    }
}
