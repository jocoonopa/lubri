<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\Test\TranZipcodeRequest;
use App\Import\Test\TranZipcodeImport;
use App\Model\City;
use App\Model\State;
use Excel;
use Illuminate\Http\Request;

class TranZipcodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        return view('test.tran_zipcode.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Import\Test\TranZipcodeImport $import
     * @return \Illuminate\Http\Response
     */
    public function store(TranZipcodeRequest $request, TranZipcodeImport $import)
    {
        return $import->handleImport();
    }
}
