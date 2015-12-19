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
    const BEFOREDAYS = 40;
    const MAILNOTIFY_EVENT_INDEX = 2;

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

    public function update(Request $request, DataHelper $dataHelper)
    {
        $event = Event::fire(new FixCPrefixGoodsEvent(self::BEFOREDAYS, Input::get('Codes')));

        if (!empty($event[self::MAILNOTIFY_EVENT_INDEX])) {
            \Session::flash('success', implode(array_fetch($event[self::MAILNOTIFY_EVENT_INDEX], 'Code'), ',') . '  贈品轉換完成!');  
        }

        return view('flap.pisgoods.fixcgoods.index', [
            'goodses' => $dataHelper->getNDaysBeforeCreatedCodes(self::BEFOREDAYS), 
            'beforeDays' => self::BEFOREDAYS
        ]);
    }
}
