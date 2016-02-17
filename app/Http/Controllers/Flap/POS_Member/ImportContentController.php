<?php

namespace App\Http\Controllers\Flap\POS_Member;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Model\Flap\PosMemberImportTask;
use App\Model\Flap\PosMemberImportTaskContent;
use Illuminate\Http\Request;
use Session;

class ImportContentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Model\Flap\PosMemberImportTask
     * @return \Illuminate\Http\Response
     */
    public function index(PosMemberImportTask $task)
    {
        return view('flap.posmember.import_task.show', [
            'task' => $task,
            'contents' => $task->content()->orderBy('status')->paginate(20),
            'title' => '任務檢視'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Model\Flap\PosMemberImportTask
     * @return \Illuminate\Http\Response
     */
    public function create(PosMemberImportTask $task)
    {
        echo __METHOD__;
        dd($task);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Model\Flap\PosMemberImportTaskContent
     * @return \Illuminate\Http\Response
     */
    public function store(PosMemberImportTaskContent $content)
    {
        echo __METHOD__;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Flap\PosMemberImportTask
     * @param  \App\Model\Flap\PosMemberImportTaskContent
     * @return \Illuminate\Http\Response
     */
    public function show(PosMemberImportTask $task, PosMemberImportTaskContent $content)
    {
        return view('flap.posmember.import_content.edit', [
            'title' => "{$content->name}編輯",
            'task' => $task,
            'content' => $content
        ]); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Flap\PosMemberImportTask
     * @param  \App\Model\Flap\PosMemberImportTaskContent
     * @return \Illuminate\Http\Response
     */
    public function edit(PosMemberImportTask $task, PosMemberImportTaskContent $content)
    {
        return view('flap.posmember.import_content.edit', [
            'title' => "{$content->name}編輯",
            'task' => $task,
            'content' => $content
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request
     * @param  \App\Model\Flap\PosMemberImportTask
     * @param  \App\Model\Flap\PosMemberImportTaskContent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PosMemberImportTask $task, PosMemberImportTaskContent $content)
    {
        $content->update($request->all());

        Session::flash('success', "項目 <b>{$content->name}</b> 更新完成!");

        return view('flap.posmember.import_task.show', [
            'task' => $task,
            'contents' => $task->content()->orderBy('status')->paginate(20),
            'title' => '任務檢視'
        ]); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Flap\PosMemberImportTaskContent
     * @return \Illuminate\Http\Response
     */
    public function destroy(PosMemberImportTaskContent $content)
    {
        echo 123;
    }
}
