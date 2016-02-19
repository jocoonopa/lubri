<?php

namespace App\Http\Controllers\Flap\POS_Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Flap\POS_Member\ImportContentRequest;
use App\Model\Flap\PosMemberImportTask;
use App\Model\Flap\PosMemberImportTaskContent;
use App\Model\State;
use Session;

class ImportContentController extends Controller
{
    public function __construct()
    {
        $this->middleware('import.content', ['only' => ['destroy', 'update', 'edit']]);
    }

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
        return view('flap.posmember.import_content.create');
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
     * @param  \Illuminate\Http\ImportContentRequest
     * @param  \App\Model\Flap\PosMemberImportTask
     * @param  \App\Model\Flap\PosMemberImportTaskContent
     * @return \Illuminate\Http\Response
     */
    public function update(ImportContentRequest $request, PosMemberImportTask $task, PosMemberImportTaskContent $content)
    {
        $this->_prevSetContentState($content, $request);

        Session::flash('success', "項目 <b>{$content->name}</b> 更新完成!");

        return redirect("/flap/pos_member/import_task/{$task_id}/content");
    }

    private function _prevSetContentState(&$content, ImportContentRequest $request)
    {
        $state = State::where('zipcode', '=', $request->get('zipcode'))->where(function ($q) use ($request) {
            $q->where('name', '=', $request->get('district'))->orWhere('pastname', '=', $request->get('district'));
        })->first();

        $content->state_id = (NULL === $state) ? NULL : $state->id;
        $content->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Flap\PosMemberImportTaskContent
     * @return \Illuminate\Http\Response
     */
    public function destroy(PosMemberImportTask $task, PosMemberImportTaskContent $content)
    {
        Session::flash('success', "項目 <b>{$content->name}</b> 已經移除!");

        $content->delete();
        $task->updateStat()->save();

        return redirect("/flap/pos_member/import_task/{$task->id}/content");
    }
}
