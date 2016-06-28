<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Model\Flap\PosMemberImportTask;
use App\Utility\Chinghwa\Flap\CCS_MemberFlags\Flater;
use App\Utility\Chinghwa\Flap\POS_Member\Import\Import;
use App\Import\Flap\POS_Member\Import AS _Import;
use Carbon\Carbon;
use Excel;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Mail;

class ImportPosMemberTask extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $task;
    protected $currentRowNum;
    protected $error;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     * @return void
     */
    public function __construct(PosMemberImportTask $task)
    {
        $this->setTask($task);
    }

    public function handle()
    {
        set_time_limit(0);

        $start = microtime(true);
        
        $this->proc();

        $end = microtime(true);

        $this->updateTaskState($this->getTask(), $start, $end);
        
        return $this->notify($this->getTask());
    }

    protected function updateTaskState($task, $start, $end)
    {
        $task->import_cost_time = floor($end - $start);
        $task->status_code = PosMemberImportTask::STATUS_TOBEPUSHED;
        $task->save();

        return $task;
    }

    protected function notify(PosMemberImportTask $task)
    {
        return Mail::send('emails.importTask', ['task' => $task], function ($m) use ($task) {
            $m->to([$task->user->email => $task->user->username])->subject("{$task->kind->name}任務{$task->name}匯入完成!");
        });
    }

    public function proc()
    {
        // $this->import->skip(1)->calculate(false)->chunk(_Import::CHUNK_SIZE, $this->getChunkCallback()); 
        // 無法處理多個 sheets 的 bug，因此改為下面的方式處理:
        //======================================================//                                   
        // 取得上傳暫存檔路徑
        $filePath = storage_path("exports/posmember/{$this->getTask()->id}.xls");

        if (!file_exists($filePath)) {
            Log::error("{$filePath} Not exists!");
        }

        // 透過直接指定選擇第一個sheet的方式，繞過 chunk 的 bug
        $reader = Excel::filter('chunk')
            ->selectSheetsByIndex(0)
            ->load($filePath)
            ->skip(1);

        $totalRows = $reader->getTotalRowsOfFile();
        $this->getTask()->status_code = PosMemberImportTask::STATUS_IMPORTING;
        $this->getTask()->total_count = $totalRows;
        $this->getTask()->save(); 
        //======================================================//
        
        $this->injectTask($totalRows);
        
        $kind         = $this->getTask()->kind()->first();            
        $factoryClass = $kind->factory;
        $adapterClass = $kind->adapter;

        $this->modelFactory = new $factoryClass;
        
        $this->adapter = new $adapterClass([
            _Import::OPTIONS_TASK        => $this->getTask(),
            _Import::OPTIONS_DISTINCTION => PosMemberImportTask::getBDSerNo($this->getTask()->distinction),
            _Import::OPTIONS_CATEGORY    => PosMemberImportTask::getCategorySerNo($this->getTask()->category),
            _Import::OPTIONS_INSERTFLAG  => Flater::getFlagString($this->getTask()->insert_flags),
            _Import::OPTIONS_UPDATEFLAG  => Flater::getFlagString($this->getTask()->update_flags)
        ]);                                    

        $reader->chunk(_Import::CHUNK_SIZE, $this->getChunkCallback());

        $this->_removeDuplicate();
        $this->_saveTaskStatic();
    }

    protected function injectTask($totalRows = 0)
    {
        $task = $this->getTask();

        $task->status_code  = PosMemberImportTask::STATUS_IMPORTING;
        $task->total_count  = $totalRows;
        $task->save();

        return $task;
    }

    /**
     * 上傳檔案時須先把多餘的工作表刪除，否則 chunk 會有問題
     *
     * Laravel Collection: https://laravel.com/docs/5.1/collections
     * 
     * @return mixed
     */
    protected function getChunkCallback() {
        return function ($collection) {
            foreach ($collection as $row) {
                $this->_iterateProcess($row);

                $this->currentRowNum ++;
            }
        };
    }

    private function _iterateProcess($row)
    {
        return (false === $this->adapter->inject($row)) 
            ? $this->_addError($row) 
            : $this->_saveNewContentModel()
        ;
    }

    private function _addError($row)
    {
        $this->error[$this->currentRowNum] = json_encode($row);

        return $this;
    }

    private function _saveNewContentModel()
    {
        try {
            $content = $this->modelFactory->create($this->adapter);
            $content->save();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return $this;
    }

    private function _removeDuplicate()
    {
        $colNames = ['cellphone', 'hometel', 'homeaddress'];

        foreach ($colNames as $colName) {
            $this->_removeDuplicateColumn($colName);
        }

        return $this;
    }

    private function _removeDuplicateColumn($colName)
    {
        return $this->getTask()->content()->isDuplicate($colName)->get()->each(function ($duplicateContent) use ($colName) {
            $this->getTask()->content()->duplicateWithThis($colName, $duplicateContent)->get()->each(function ($toBeRemoveContent) {
                $toBeRemoveContent->delete();
            });
        });
    }

    private function _saveTaskStatic()
    {
        $task = $this->getTask();
        $task->error = json_encode($this->error);
        $task->updateStat()->save();

        return $task;
    }

    /**
     * Gets the value of task.
     *
     * @return mixed
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Sets the value of task.
     *
     * @param mixed $task the task
     *
     * @return self
     */
    protected function setTask($task)
    {
        $this->task = $task;

        return $this;
    }
}