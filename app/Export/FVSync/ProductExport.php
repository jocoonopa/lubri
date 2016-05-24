<?php

namespace App\Export\FVSync;

use Carbon\Carbon;

class ProductExport extends \Maatwebsite\Excel\Files\NewExcelFile
{
    public function getFilename()
    {   
        return 'Product_' . time();
    }
}