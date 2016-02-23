<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import;

use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Model\Flap\PosMemberImportTask;
use App\Model\Flap\PosMemberImportTaskContent;
use Auth;
use Input;

class ImportHandler implements \Maatwebsite\Excel\Files\ImportHandler 
{
    /**
     * Data adapter
     * 
     * @var App\Utility\Chinghwa\Flap\POS_Member\Import\ImportColumnAdapter
     */
    protected $adapter;

    /**
     * Factory of App\Model\Flap\PosMemberImportTaskContent
     * 
     * @var App\Utility\Chinghwa\Flap\POS_Member\Import\ImportModelFactory
     */
    protected $modelFactory;

    /**
     * @var App\Model\Flap\PosMemberImportTask;
     */
    protected $task;

    /**
     * Current excel row number
     * 
     * @var integer
     */
    protected $currentRowNum;

    /**
     * Error msg of current import row
     * 
     * @var array
     */
    protected $error;

    /**
     * Handle the result properly, and then return it to controller
     * 
     * @param  $import
     * @return mixed
     */
    public function handle($import)
    {
        $this->adapter = new ImportColumnAdapter(
            [
                Import::OPTIONS_DISTINCTION => PosMemberImportTask::getBDSerNo(Input::get(Import::OPTIONS_DISTINCTION)),
                Import::OPTIONS_CATEGORY    => PosMemberImportTask::getCategorySerNo(Input::get(Import::OPTIONS_CATEGORY)),
                Import::OPTIONS_INSERTFLAG  => Input::get(Import::OPTIONS_INSERTFLAG),
                Import::OPTIONS_UPDATEFLAG  => Input::get(Import::OPTIONS_UPDATEFLAG)
            ]
        );

        $this->modelFactory = new ImportModelFactory;
        $this->task = $this->createNewTask();

        $import->skip(1)->calculate(false)->chunk(Import::CHUNK_SIZE, $this->getChunkCallback());

        return $this->_removeDuplicate()->_saveTaskStatic();
    }

    protected function createNewTask()
    {
        $task = new PosMemberImportTask;

        $task->user_id      = Auth::user()->id;
        $task->name         = Input::get('name');
        $task->distinction  = Input::get(Import::OPTIONS_DISTINCTION);
        $task->category     = Input::get(Import::OPTIONS_CATEGORY);
        $task->update_flags = $this->adapter->getUpdateFlagPairs();
        $task->insert_flags = $this->adapter->getInsertFlagPairs();
        $task->save();

        return $task;
    }

    /**
     * 上傳檔案時須先把多餘的工作表刪除，否則 chunk 會有問題
     * 
     * @return mixed
     */
    protected function getChunkCallback() {
        return function ($sheet) {
            foreach ($sheet as $row) {                    
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
        $this->modelFactory->create($this->adapter, $this->task)->save();

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
        return $this->task->content()->isDuplicate($colName)->get()->each(function ($duplicateContent) use ($colName) {
            $this->task->content()->duplicateWithThis($colName, $duplicateContent)->get()->each(function ($toBeRemoveContent) {
                $toBeRemoveContent->delete();
            });
        });
    }

    private function _saveTaskStatic()
    {
        $this->task->error = json_encode($this->error);
        $this->task->updateStat()->save();

        return $this->task;
    }
} 