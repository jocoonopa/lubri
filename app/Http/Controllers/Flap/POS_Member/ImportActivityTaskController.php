<?php

namespace App\Http\Controllers\Flap\Pos_Member;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\Flap\POS_Member\ImportActivityTaskRequest;
use App\Model\Flap\PosMemberImportActivityTask;
use App\Import\Flap\POS_Member\ImportActivityTask\Import;
use Illuminate\Http\Request;

class ImportActivityTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('flap.posmember.import_activity_task.index', [
            'tasks' => PosMemberImportActivityTask::latest()->paginate(20),
            'title' => '任務列表'
        ]);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('flap.posmember.import_activity_task.create', ['title' => '會員活動名單匯入任務建立', 'task' => with(new PosMemberImportActivityTask)]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ImportActivityTaskRequest $request, Import $import)
    {
        $import->handleImport();
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
