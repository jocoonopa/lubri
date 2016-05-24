<?php

namespace App\Export\FVSync;

use Carbon\Carbon;

class ProductExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
    public function handle($export)
    {
        $export->store('xlsx', storage_path('excel/exports'));

        return $export;
    }
}