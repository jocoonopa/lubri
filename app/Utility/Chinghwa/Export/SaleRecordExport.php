<?php

namespace App\Utility\Chinghwa\Export;

class SaleRecordExport extends \Maatwebsite\Excel\Files\NewExcelFile
{
    public function getFilename()
    {
        return 'Sale_Record_' . date('Y');
    }
}