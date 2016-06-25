<?php

namespace App\Export\FV\Sync;

use Carbon\Carbon;

class OrderExport extends \Maatwebsite\Excel\Files\NewExcelFile
{
    protected $name;

    public function getFilename()
    {   
        return 'Order_' . time();
    }
}