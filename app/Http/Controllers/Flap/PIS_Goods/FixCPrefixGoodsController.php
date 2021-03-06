<?php

namespace App\Http\Controllers\Flap\PIS_Goods;

use App\Http\Requests\Flap\PIS_Goods\FixCPrefixRequest;
use App\Http\Controllers\Controller;
use App\Events\Flap\PIS_Goods\FixCPrefixGoodsEvent;
use App\Utility\Chinghwa\Helper\Flap\PIS_Goods\FixCPrefixGoods\DataHelper;
use Event;
use Input;

class FixCPrefixGoodsController extends Controller
{
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
            'goodses' => $dataHelper->getNDaysBeforeCreatedCodes(env('FIXCPREFIXGOODS_BEFOREDAYS')), 
            'beforeDays' => env('FIXCPREFIXGOODS_BEFOREDAYS')
        ]);
    }

    public function update(FixCPrefixRequest $request)
    {
        $event = Event::fire(new FixCPrefixGoodsEvent(env('FIXCPREFIXGOODS_BEFOREDAYS'), Input::get('Codes')));

        if (!empty($event[self::MODIFYGOODS_EVENT_INDEX])) {
            \Session::flash('success', "{$event[self::MODIFYGOODS_EVENT_INDEX]}贈品轉換完成!<br/>{$event[self::MAILNOTIFY_EVENT_INDEX]}");  
        }

        return redirect()->route('pis_goods_fix_cprefix_goods_index');
    }
}
