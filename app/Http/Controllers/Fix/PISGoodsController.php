<?php

namespace App\Http\Controllers\Fix;

use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\Helper\PISGoodsImportQueryHelper;

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

        $insertRows = $this->getInsertRows($queryHelper);
        $lastSerNo = $this->getLastSerNo($queryHelper);

        $this->displayAllQuery($queryHelper, $insertRows, $lastSerNo);

        return;
    }

    protected function getInsertRows(PISGoodsImportQueryHelper $queryHelper)
    {
        $insertRows = [];

        $selectQuery = $queryHelper->genSelectQuery();
        if ($res = $this->execute($selectQuery)) {
            while ($row = odbc_fetch_array($res)) {
                $this->c8res($row);
                $insertRows[] = $row;
            }
        }

        return $insertRows;
    }

    protected function getLastSerNo(PISGoodsImportQueryHelper $queryHelper)
    {
        $lastSerNoQuery = $queryHelper->genFetchLastSerNoQuery();
        if ($res = $this->execute($lastSerNoQuery)) {
            while ($row = odbc_fetch_array($res)) {
                $this->c8res($row);
                $lastSerNo = $row['SerNo'];
            }
        }

        return $lastSerNo;
    }

    protected function displayAllQuery(PISGoodsImportQueryHelper $queryHelper, array $insertRows, $lastSerNo)
    {
        foreach ($insertRows as $insertRow) {
            $insertQuery = $queryHelper->genInsertQuery($insertRow, $lastSerNo);

            echo "{$insertQuery}<br />";
        }

        return $this;
    }
}

