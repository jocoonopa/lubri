<?php

namespace App\Http\Controllers\Flap\POS_Member;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\Flap\POS_Member\ImportTaskRequest;
use App\Model\Flap\PosMemberImportContent;
use App\Model\Flap\PosMemberImportKind;
use App\Model\Flap\PosMemberImportTask;
use App\Utility\Chinghwa\Database\Connectors\Connector;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\Export\ImportTaskExport;
use App\Utility\Chinghwa\Flap\POS_Member\Import\Import;
use App\Utility\Chinghwa\Flap\POS_Member\Import\ImportFilter;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Input;
use Response;
use Session;

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
            'tasks' => PosMemberImportTask::findByKind(Input::get('kind_id'))->latest()->paginate(20),
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
        set_time_limit(0);
        
        return redirect()->action('Flap\POS_Member\ImportTaskController@show', ['import_task' => $this->proxyStore($import)->id]);
    }

    protected function proxyStore(Import $import)
    {
        $start = microtime(true);
        
        $task = $import->handleImport();

        $end = microtime(true);

        $task->import_cost_time = floor($end - $start);
        $task->save();

        Session::flash('success', "成功新增任務{$task->name}@{$task->kind()->first()->name}!");

        if (0 === $task->content->count()) {
            Session::flash('error', "任務裡面沒有任何內容，請確認上傳 xls 檔案僅有一個工作表");
        }

        return $task;
    }

    public function destroy(Request $request, PosMemberImportTask $task)
    {
        $task->delete();

        Session::flash('success', "成功移除任務{$task->name}!");

        return redirect("/flap/pos_member/import_task?kind_id={$task->kind()->first()->id}");
    }

    public function importProgress()
    {
        return PosMemberImportTask::latest()
            ->where('user_id', Auth::user()->id)
            ->where('import_cost_time', 0)
            ->where('insert_count', 0)
            ->where('update_count', 0)
            ->where('created_at', '>=', Carbon::now()->modify('-10 minutes'))
            ->first()->content->count()
        ;
    }

    public function pullProgress(PosMemberImportTask $task)
    {
        return $task->content()->isNotExecuted()->where('updated_at', '>=', $task->updated_at->format('Y-m-d H:i:s'))->count();
    }

    public function pushProgress(PosMemberImportTask $task)
    {
        return $task->content()->where(DB::raw('32&Status'), '=', 32)->count();        
    }

    public function export(Request $request, ImportTaskExport $export, PosMemberImportTask $task)
    {
        set_time_limit(0);

        if ($request->ajax()) {
            return $export->setTask($task)->handleExport();
        }
        
        return Response::download(Input::get('f'), "{$task->name}_{$task->updated_at->format('YmdH')}.xls", ['Content-Type: application/excel']);
    }
}
