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
        return Excel::create('zipcode', function ($excel) {
            $import->chunk(TranZipcodeImport::CHUNK_SIZE, $this->getChunkCallback($excel));
        })->download();        
    }

    protected function getChunkCallback($excel)
    {
        return function ($sheet) use ($excel) {
            $sheet->each(function ($row) use ($excel) {
                $this->_iterateProcess($row, $excel);
            });      
        };
    }

    private function _iterateProcess($row, $excel)
    {
        return $excel->sheet('zipcode', $this->_appendRowCallback($row, $excel));                             
    }

    private function _appendRowCallback()
    {
        return function ($sheet) use ($row) {
            $sheet->rows($this->_getRowData($row));
        }
    }

    private function _getRowData($row)
    {
        $cityName = trim(keepOnlyChineseWord(array_get($row, 0)));
        $stateName = trim(keepOnlyChineseWord(array_get($row, 1)));

        $city = City::findByName($cityName);
        $state = NULL !== $city ? State::findByName($stateName) : NULL;                            
        $zipcode = NULL !== $state ? $state->zipcode : '';

        return [$cityName, $stateName, $zipcode];
    }
} 