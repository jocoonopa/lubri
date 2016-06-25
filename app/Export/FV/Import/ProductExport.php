<?php

namespace App\Export\FV\Import;

use App\Export\Mould\FVMProductMould;

class ProductExport extends FVImportExport
{
    public function getType()
    {
        return 'product';
    }

    public function getMould()
    {
        return new FVMProductMould;
    }
}