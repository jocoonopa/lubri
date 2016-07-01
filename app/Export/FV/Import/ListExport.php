<?php

namespace App\Export\FV\Import;

use App\Export\Mould\FVListMould;

class ListExport extends FVImportExport
{
    public function getMould()
    {
        return new FVListMould;
    }

    public function getType()
    {
        return 'list';
    }

    public function getStartDate()
    {
        return '2016-07-01 00:00:00';
    }
}