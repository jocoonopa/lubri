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
     * 上傳
     * 
     * @return \Illuminate\Http\Response
     */
    public function upload()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
