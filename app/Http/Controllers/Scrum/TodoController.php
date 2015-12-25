<?php

namespace App\Http\Controllers\Scrum;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $storys = [
            [
                'icon' => 'hourglass_empty',
                'point' => '1',
                'index' => '1',
                'heri' => '',
                'content' => '總經理每天早上九點收到報告信'
            ],
            [
                'icon' => 'hourglass_empty',
                'point' => '1/2',
                'index' => '2',
                'heri' => '1',
                'content' => '總經理下載信中夾帶的Excel檔案'
            ],
            [
                'icon' => 'hourglass_empty',
                'point' => '2',
                'index' => 'A3',
                'heri' => '2',
                'content' => '總經理選擇Excel的各通路平均貢獻金額工作表'
            ],
            [
                'icon' => 'hourglass_empty',
                'point' => '4',
                'index' => 'A4',
                'heri' => 'A3',
                'content' => '總經理從各通路平均貢獻金額工作表的部門欄找到EC部門的資料列'
            ],
            [
                'icon' => 'hourglass_empty',
                'point' => '20',
                'index' => 'A5',
                'heri' => 'A4',
                'content' => '總經理瀏覽資料列的平均會員貢獻金額'
            ]
        ];

        return view('scrum.todo.index', ['storys' => $storys]);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
