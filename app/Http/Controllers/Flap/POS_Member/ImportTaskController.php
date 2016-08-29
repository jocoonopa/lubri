<?php

namespace App\Http\Controllers\Flap\POS_Member;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\Flap\POS_Member\ImportTaskRequest;
use App\Import\Flap\POS_Member\Import AS _Import;
use App\Jobs\ImportPosMemberTask;
use App\Model\Flap\PosMemberImportContent;
use App\Model\Flap\PosMemberImportKind;
use App\Model\Flap\PosMemberImportTask;
use App\Utility\Chinghwa\Database\Connectors\Connector;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\Export\ImportTaskExport;
use App\Utility\Chinghwa\Flap\CCS_MemberFlags\Flater;
use App\Utility\Chinghwa\Flap\POS_Member\Import\Import;
use App\Utility\Chinghwa\Flap\POS_Member\Import\ImportFilter;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Input;
use Response;
use Session;

/**
 * --------------------------------------- 匯入部分說明 -----------------------------------------
 * 
 * 1. 不論是匯入, 更新, 推送的 progress, 統一在 list 頁面點擊 button 查看(invoke taskProgress(), implement with modal)
 * 2. 狀態為匯入中, 更新中, 或是推送中，隱藏原本的操作按鈕，改為顯示一動態 css3 loading 圖示, 以及一個查看進度按鈕[B1]
 * 3. 點擊[B1]會pop 一個 modal[M1], [M1] 有 progress bar, 總處理筆數以及目前已經處理筆數
 * 
 * 其中匯入這邊做法流程要稍微調整。
 * 必須先建立 importProgress 的 Que 機制
 * 
 */
class ImportTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('import.kind', ['only' => ['index', 'create', 'store', 'destroy']]);
        $this->middleware('import.task', ['only' => ['destroy']]);
        $this->middleware('ajax', ['only' => ['pushProgress', 'pullProgress', 'importProgress']]);
    }

    public function edit(PosMemberImportTask $task)
    {        
        $importKind = $task->kind()->first();

        return view($importKind->edit_view_path, ['title' => "任務 {$task->name} 編輯", 'task' => $task]);
    }

    public function update(ImportTaskRequest $request, PosMemberImportTask $task)
    {
        $this->_update($request, $task);

        Session::flash('success', "成功修改任務{$task->name}!");

        return redirect()->action('Flap\POS_Member\ImportTaskController@show', ['import_task' => $task->id]);
    }

    private function _update(ImportTaskRequest $request, PosMemberImportTask $task)
    {
        $task->update_flags = PosMemberImportTask::getInflateFlag($request->get('updateFlagString'));

        $task->insert_flags = PosMemberImportTask::getInflateFlag($request->get('insertFlagString'));
        
        $task->update($request->all());
    }

    public function index(Request $request)
    {
        return view('flap.posmember.import_task.index', [
            'tasks' => PosMemberImportTask::findByKind(Input::get('kind_id'))->latest()->paginate(10),
            'title' => PosMemberImportKind::find($request->get('kind_id'))->name
        ]);
    }

    public function show(PosMemberImportTask $task)
    {      
        $contents = $task->content()->nullColumnFilter(Input::except(['page', 'is_exist']));

        if (Input::get('is_exist')) {
            $contents->where('is_exist', '=', 'yes' === Input::get('is_exist'));
        }

        return view('flap.posmember.import_task.show', [
            'task'     => $task,
            'count'    => $contents->count(),
            'contents' => $contents->orderBy('status')->paginate(20),            
            'title'    => '任務檢視'
        ]);
    }

    public function create(Request $request)
    {
        $importKind = PosMemberImportKind::find($request->get('kind_id'));

        return view($importKind->create_view_path, [
            'title' => "{$importKind->name}任務建立", 
            'task'  => with(new PosMemberImportTask)
        ]);
    }

    /**
     * The inject priority must be corrected, otherwise the validation will be failed
     *
     * @param  ImportTaskRequest   $request
     * @param  PosMemberImport $import 
     * @return \Illuminate\Http\Response                  
     */
    public function store(ImportTaskRequest $request, Import $import)
    {
        try {
            $task = $this->createNewTask();

            $path = $import->skip(0)->file;

            if (file_exists($path)) {
                copy($path, storage_path("exports/posmember/{$task->id}.xls"));
            }

            $job = with(new ImportPosMemberTask($task))->onQueue(env('IRON_QUEUE'))->delay(30);
            $this->dispatch($job);

            $task->status_code = PosMemberImportTask::STATUS_IMPORTING;
            $task->save();

            Session::flash('success', "成功新增任務{$task->name}@{$task->kind()->first()->name}!");

            return redirect("/flap/pos_member/import_task?kind_id={$task->kind()->first()->id}");
        } catch (\Exception $e) {
            $task->delete();

            pr($e->getMessage());
        }
    }

    protected function createNewTask()
    {
        $task = new PosMemberImportTask;

        $task->user_id      = Auth::user()->id;
        $task->name         = Input::get('name');
        $task->status_code  = PosMemberImportTask::STATUS_INIT;
        $task->distinction  = Input::get(_Import::OPTIONS_DISTINCTION);
        $task->category     = Input::get(_Import::OPTIONS_CATEGORY);
        $task->update_flags = Flater::getInflateFlag(Input::get(_Import::OPTIONS_UPDATEFLAG));
        $task->insert_flags = Flater::getInflateFlag(Input::get(_Import::OPTIONS_INSERTFLAG));
        $task->kind_id      = Input::get('kind_id');
        $task->memo         = Input::get(_Import::OPTIONS_OBMEMO);
        $task->save();

        return $task;
    }

    public function destroy(Request $request, PosMemberImportTask $task)
    {
        $task->delete();

        Session::flash('success', "成功移除任務{$task->name}!");

        return redirect("/flap/pos_member/import_task?kind_id={$task->kind()->first()->id}");
    }

    /**
     * Return json string response
     *
     * @return mixed json response
     */
    public function progress(PosMemberImportTask $task)
    {
        return response()->json([
            'id'             => $task->id, 
            'name'           => $task->name,
            'kind_name'      => $task->kind->name,
            'status_code'    => $task->status_code,
            'status_name'    => strip_tags($task->getStatusName()),
            'total'          => $task->total_count,
            'imported_count' => $task->content->count(),
            'pushed_count'   => $task->content()->where(DB::raw(PosMemberImportTask::BEEN_PUSHED_FLAG . '&Status'), '=', PosMemberImportTask::BEEN_PUSHED_FLAG)->count(),
            'is_acting' => in_array($task->status_code, [PosMemberImportTask::STATUS_IMPORTING, PosMemberImportTask::STATUS_PUSHING])
        ]);
    }

    public function export(Request $request, ImportTaskExport $export, PosMemberImportTask $task)
    {
        set_time_limit(0);

        return $export->setTask($task)->handleExport()->export();
    }
}
