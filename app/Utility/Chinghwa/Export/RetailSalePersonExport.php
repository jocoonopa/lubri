<?php

namespace App\Utility\Chinghwa\Export;

class RetailSalePersonExport extends \Maatwebsite\Excel\Files\NewExcelFile
{
    public function getFilename()
    {
        return 'Retail_Sale_Person_' . date('Ym');
    }

    public function getRealpath()
    {
        return __DIR__ . '/../../../../storage/excel/exports/' . $this->getFilename() . '.xls';
    }
}