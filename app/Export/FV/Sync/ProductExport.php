<?php

namespace App\Export\FV\Sync;

use Carbon\Carbon;

class ProductExport extends \Maatwebsite\Excel\Files\NewExcelFile
{
    public function getFilename()
    {   
        return 'Product_' . time();
    }
}