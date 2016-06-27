<?php

namespace App\Export\FV\Import;

use App\Export\Mould\FVOrderMould;

class OrderExport extends FVImportExport
{
    public function getType()
    {
        return 'order';
    }

    public function getMould()
    {
        return new FVOrderMould;
    }
}