<?php

namespace App\Http\Controllers\Flap\CCS_ReturnGoodsI;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CancelVerifyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('basic.simple', [
            'title' => '取消退貨覆核', 
            'des' => '<h4>取消退貨覆核</h4><pre>' . $this->getQuery() . '</pre>',
            'res' => NULL
        ]);
    }

    protected function getQuery()
    {
        return "UPDATE CCS_ReturnGoodsI SET VerifySerNo=NULL, VerifyTime=NULL, VerifyDate=NULL WHERE No='SomeNo'";
    }
}
