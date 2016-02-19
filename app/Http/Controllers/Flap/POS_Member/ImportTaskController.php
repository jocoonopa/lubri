<?php

namespace App\Http\Controllers\Flap\POS_Member;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\Flap\POS_Member\ImportTaskRequest;
use App\Model\Flap\PosMemberImportTask;
use App\Model\Flap\PosMemberImportTaskContent;
use App\Utility\Chinghwa\Database\Connectors\Connector;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\Flap\POS_Member\Import\Import;
use App\Utility\Chinghwa\Flap\POS_Member\Import\ImportFilter;
use DB;
use Illuminate\Http\Request;
use Session;

class ImportTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('import.task', ['only' => ['destroy']]);
        $this->middleware('ajax', ['only' => ['pushProgress', 'pullProgress', 'importProgress']]);
    }

    public function create(Request $reqeust)
    {
        return view('flap.posmember.import_task.create', ['title' => '麗嬰房會員名單匯入']);
    }

    public function edit(PosMemberImportTask $task){}

    public function index(Request $request)
    {
        return view('flap.posmember.import_task.index', [
            'tasks' => PosMemberImportTask::latest()->paginate(20),
            'title' => '任務列表'
        ]);
    }

    public function show(PosMemberImportTask $task)
    {
        return view('flap.posmember.import_task.show', [
            'task' => $task,
            'contents' =>$task->content()->orderBy('status')->paginate(20),
            'title' => '任務檢視'
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

        Session::flash('success', "成功新增任務{$task->id}!");

        if (0 === $task->content->count()) {
            Session::flash('error', "任務裡面沒有任何內容，請確認上傳 xls 檔案僅有一個工作表");
        }

        return $task;
    }

    public function destroy(Request $request, PosMemberImportTask $task)
    {
        $task->delete();

        Session::flash('success', "成功移除任務{$task->id}!");

        return redirect()->action('Flap\POS_Member\ImportTaskController@index');
    }

    public function importProgress()
    {
        return PosMemberImportTask::latest()->first()->content->count();
    }

    public function pullProgress(PosMemberImportTask $task)
    {
        return $task->content()->isNotExecuted()->where('updated_at', '>=', $task->updated_at->format('Y-m-d H:i:s'))->count();
    }

    public function pushProgress(PosMemberImportTask $task)
    {
        return $task->content()->where(DB::raw('32&Status'), '=', 32)->count();        
    }
}
