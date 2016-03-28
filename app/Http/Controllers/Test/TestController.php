<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Model\City;
use App\Model\State;
use Excel;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function toYiHua()
    {        
        $dataSpool = [];

        Excel::selectSheetsByIndex(0)->load('app\Http\Controllers\Test\text.xls', function($reader) {})
            ->get()->each(function ($row) use (&$dataSpool) {
                $cityName = trim(keepOnlyChineseWord(array_get($row, 0)));
                $stateName = trim(keepOnlyChineseWord(array_get($row, 1)));

                $state = null;
                $city = City::findByName($cityName);

                if ($city) {
                    $state = State::findByName($stateName);                    
                }                

                $dataSpool[] = (null !== $city && null !== $state) 
                    ? [$city->first()->name, $state->first()->name, $state->first()->zipcode]
                    : [$cityName, $stateName, '000']
                ;           
            });       

        Excel::create('test', function ($excel) use ($dataSpool) {
            $excel->sheet('Sheetname', function ($sheet) use ($dataSpool) {
                $sheet->rows($dataSpool);
            });   
        })->download();
    }
}
