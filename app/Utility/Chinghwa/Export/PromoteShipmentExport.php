<?php

namespace App\Utility\Chinghwa\Export;

class PromoteShipmentExport extends \Maatwebsite\Excel\Files\NewExcelFile 
{
    public function getFilename()
    {
        return "PromoteShipmentExport_" . time();
    }
}