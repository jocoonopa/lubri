<?php

namespace App\Import\Test;

use App\Import\Test\TranZipcodeImport;
use App\Model\City;
use App\Model\State;
use Excel;

class TranZipcodeImportHandler implements \Maatwebsite\Excel\Files\ImportHandler 
{
    /**
     * Handle the result properly, and then return it to controller
     * 
     * @param  $import
     * @return mixed
     */
    public function handle($import)
    {
        return Excel::create('zipcode', function ($excel) use ($import) {
            $excel->sheet('FirstSheet', function($exportSheet) use ($import) {
                $import->chunk(TranZipcodeImport::CHUNK_SIZE, $this->getChunkCallback($exportSheet));
            });            
        })->download();        
    }

    protected function getChunkCallback($exportSheet)
    {
        return function ($sheet) use ($exportSheet) {
            $sheet->each(function ($row) use ($exportSheet) {
                $this->_iterateProcess($row, $exportSheet);
            });      
        };
    }

    private function _iterateProcess($row, $exportSheet)
    {
        $exportSheet->appendRow($this->_getRowData($row));                            
    }

    private function _getRowData($row)
    {
        $cityName = trim(keepOnlyChineseWord(array_get($row, 0)));
        $stateName = trim(keepOnlyChineseWord(array_get($row, 1)));

        $city = City::findByName($cityName)->first();
        $state = (NULL !== $city) ? State::findByName($stateName)->first() : NULL;                            
        $zipcode = (NULL !== $state) ? $state->zipcode : '';

        return ('' !== $zipcode) ? [$cityName, $stateName, $zipcode] : [array_get($row, 0), array_get($row, 1), ''];
    }
} 