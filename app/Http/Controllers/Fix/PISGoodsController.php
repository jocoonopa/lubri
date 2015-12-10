<?php

namespace App\Http\Controllers\Fix;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\Helper\PISGoodsImportQueryHelper;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;

class PISGoodsController extends Controller
{
    /**
     * 0. BUILD 3000 QUERY
     * 1. EXEC SELECT 3000
     * 2. MODIFY SN WITH SPECIFIC RULE
     * 3. INSERT QUERY BUILD
     * 4. EXECUTE INSERT QUERY
     */
    public function import()
    {
        $queryHelper = new PISGoodsImportQueryHelper();
        $insertRows = [];
        $lastSerNo = NULL;

        $this
            ->odbcFetchArray($queryHelper->genSelectQuery(), $this->getInsertFunc(), $insertRows)
            ->odbcFetchArray($queryHelper->genFetchLastSerNoQuery(), $this->getLastFunc(), $lastSerNo)
        ;

        $this->displayAllQuery($queryHelper, $insertRows, $lastSerNo);

        return;
    }

    protected function getInsertFunc()
    {
        return function (&$insertRows, $row) {
            $insertRows[] = $row;
        };
    }

    protected function getLastFunc()
    {
        return function (&$lastSerNo, $row) {
            $lastSerNo = $row['SerNo'];
        };
    }

    protected function displayAllQuery(PISGoodsImportQueryHelper $queryHelper, array $insertRows, $lastSerNo)
    {
        foreach ($insertRows as $insertRow) {
            $insertQuery = $queryHelper->genInsertQuery($insertRow, $lastSerNo);

            echo "{$insertQuery}<br />";
        }

        return $this;
    }

    protected function odbcFetchArray($query, $callback, &$src)
    {
        Processor::fetchArray($query, $callback, $src);

        return $this;
    }
}

