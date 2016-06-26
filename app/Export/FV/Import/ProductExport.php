<?php

namespace App\Export\FV\Import;

use App\Export\Mould\FVProductMould;

class ProductExport extends FVImportExport
{
    public function getType()
    {
        return 'product';
    }

    public function getMould()
    {
        return new FVProductMould;
    }
}