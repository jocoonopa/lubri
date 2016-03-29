<?php

namespace App\Import\Test;

class TranZipcodeImport extends \Maatwebsite\Excel\Files\ExcelFile 
{
    const CHUNK_SIZE = 500;
    
    public function getFile()
    {
        return storage_path('exports') . '/tran_zipcode_tmp.csv';
    }

    public function getFilters()
    {
        return [
            'Maatwebsite\Excel\Filters\ChunkReadFilter',
            'chunk'
        ];
    }
}