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
    const BEFOREDAYS              = 10;
    const MODIFYGOODS_EVENT_INDEX = 1;
    const MAILNOTIFY_EVENT_INDEX  = 2;

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
        if (empty(Input::get('Codes'))) {
            return redirect()->route('pis_goods_fix_cprefix_goods_index');
        }

        $event = Event::fire(new FixCPrefixGoodsEvent(self::BEFOREDAYS, Input::get('Codes')));

        if (!empty($event[self::MODIFYGOODS_EVENT_INDEX])) {
            \Session::flash('success', "{$event[self::MODIFYGOODS_EVENT_INDEX]}贈品轉換完成!<br/>{$event[self::MAILNOTIFY_EVENT_INDEX]}");  
        }

        return redirect()->route('pis_goods_fix_cprefix_goods_index');
    }
}
