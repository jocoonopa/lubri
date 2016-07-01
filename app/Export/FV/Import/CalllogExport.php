<?php

namespace App\Export\FV\Import;

use App\Export\Mould\FVCalllogMould;

class CalllogExport extends FVImportExport
{
    public function getMould()
    {
        return new FVCalllogMould;
    }

    public function getType()
    {
        return 'calllog';
    }

    public function getStartDate()
    {
        return '2016-07-01 00:00:00';
    }
}