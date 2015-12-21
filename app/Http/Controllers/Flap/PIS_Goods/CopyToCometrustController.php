<?php

namespace App\Http\Controllers\Flap\PIS_Goods;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\Flap\PIS_Goods\CopyToCometrustRequest;
use App\Events\Flap\PIS_Goods\CopyToCometrust\FindEvent;
use App\Events\Flap\PIS_Goods\CopyToCometrust\CopyEvent;
use Event;
use Input;
use Session;

class CopyToCometrustController extends Controller
{
    const COPY_FROM_ERP_EVENT_INDEX = 2; 

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $goodses = [];

        Session::forget('error');

        if (Input::get('code')) {
            $event = Event::fire(new FindEvent(Input::get('code')));

            $goodses = $event[0]->getGoodses();

            if (!empty($massCodes = $event[0]->getMassCodes())) {
                Session::flash('error', '商品編號: <b>' . implode($massCodes, ',') . '</b> 不存在或非景華商品');
            }
        }

        return view('flap.pisgoods.copytocometrust.index', [
            'goodses' => $goodses
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Flap\PIS_Goods\CopyToCometrustRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CopyToCometrustRequest $request)
    {
        $event = Event::fire(new CopyEvent(Input::get('Codes')));

        if (!empty($event[self::COPY_FROM_ERP_EVENT_INDEX])) {
            Session::flash('success', "<b>{$event[self::COPY_FROM_ERP_EVENT_INDEX]}</b> 複製完成!");
        }

        return redirect()->route('pis_goods_copy_to_cometrust_index');
    }
}
