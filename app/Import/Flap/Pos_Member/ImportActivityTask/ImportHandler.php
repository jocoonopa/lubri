<?php

namespace App\Import\Flap\POS_Member\ImportActivityTask;

use App\Utility\Chinghwa\Flap\POS_Member\Import\ImportFilter;
use DB;

class ImportHandler implements \Maatwebsite\Excel\Files\ImportHandler
{
    /**
     * @var App\Model\Flap\PosMemberImportTask;
     */
    protected $task;

    /**
     * Current excel row number
     * 
     * @var integer
     */
    protected $currentRowNum;

    /**
     * Error msg of current import row
     * 
     * @var array
     */
    protected $error;

    /**
     * Handle the result properly, and then return it to controller
     * 
     * @param  $import
     * @return mixed
     */
    public function handle($import)
    {
        DB::transaction(function () use ($import) {
            $import->skip(1)->calculate(false)->chunk(Import::CHUNK_SIZE, $this->getChunkCallback(new ImportFilter));
        });        
    }

    /**
     * 上傳檔案時須先把多餘的工作表刪除，否則 chunk 會有問題
     * 
     * @return mixed
     */
    protected function getChunkCallback($filter) {
        return function ($sheet) use ($filter) {
            $sheet->each($this->getIterateProcess($filter));  

            $this->currentRowNum ++;                      
        };
    }

    protected function getIterateProcess() {
        return function ($row) use ($filter) {
            $state = $filter->clearCacheState()->getState(Import::DEFAULT_ZIPCODE, $row[3]);
            
            if (NULL === $state) {
                echo "{$row[3]}<br/>";
            } 
        };
    }
} 