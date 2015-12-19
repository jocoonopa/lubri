<?php

namespace App\Http\Controllers\Flap\PIS_Goods;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Events\Flap\PIS_Goods\FixCPrefixGoodsEvent;
use App\Utility\Chinghwa\Helper\Flap\PIS_Goods\FixCPrefixGoods\DataHelper;
use Event;
use Input;

class FixCPrefixGoodsController extends Controller
{
    const BEFOREDAYS = 10;

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(DataHelper $dataHelper)
    {
        return view('flap.pisgoods.fixcgoods.index', [
            'goodses' => $dataHelper->getNDaysBeforeCreatedCodes(self::BEFOREDAYS), 
            'beforeDays' => self::BEFOREDAYS
        ]);
    }

    public function update(Request $request)
    {
        return Event::fire(new FixCPrefixGoodsEvent(self::BEFOREDAYS, Input::get('Codes')));
    }
}
