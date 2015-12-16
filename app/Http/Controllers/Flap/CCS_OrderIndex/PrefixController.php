<?php

namespace App\Http\Controllers\Flap\CCS_OrderIndex;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Events\Flap\CCS_OrderIndex\PrefixEvent;
use Event;
use Input;

class PrefixController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        return Event::fire(new PrefixEvent());
    }
}
