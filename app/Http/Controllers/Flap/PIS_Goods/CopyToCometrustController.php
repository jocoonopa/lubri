<?php

namespace App\Http\Controllers\Flap\PIS_Goods;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\Flap\PIS_Goods\CopyToCometrustRequest;
use App\Events\Flap\PIS_Goods\CopyToCometrust\FindEvent;
use Event;
use Input;

class CopyToCometrustController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Input::get('code')) {
            Event::fire(new FindEvent(Input::get('code')));
        }

        return view('flap.pisgoods.copytocometrust.index', ['goodses' => []]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }
}
