<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import;

use App\Import\Flap\POS_Member\Import AS _Import;
use App\Model\Flap\PosMemberImportTask;
use App\Model\Flap\PosMemberImportContent;
use App\Utility\Chinghwa\Flap\CCS_MemberFlags\Flater;
use App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler\Lyin\Adapter;
use App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler\Lyin\ModelFactory;
use Auth;
use DB;
use Input;

class ImportHandler implements \Maatwebsite\Excel\Files\ImportHandler 
{
    protected $import;

    /**
     * Data adapter
     * 
     * @var App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler\Act\Adapter
     */
    protected $adapter;

    /**
     * Factory of App\Model\Flap\PosMemberImportContent
     * 
     * @var App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler\Act\ModelFactory
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
        $this->import = $import;

        //DB::transaction(function() {
            $this->task = $this->createNewTask();

            $kind = $this->task->kind()->first();            
            $factoryClass = $kind->factory;
            $adapterClass = $kind->adapter;

            $this->modelFactory = new $factoryClass;
            
            $this->adapter = new $adapterClass([
                _Import::OPTIONS_TASK        => $this->task,
                _Import::OPTIONS_DISTINCTION => PosMemberImportTask::getBDSerNo(Input::get(_Import::OPTIONS_DISTINCTION)),
                _Import::OPTIONS_CATEGORY    => PosMemberImportTask::getCategorySerNo(Input::get(_Import::OPTIONS_CATEGORY)),
                _Import::OPTIONS_INSERTFLAG  => Input::get(_Import::OPTIONS_INSERTFLAG),
                _Import::OPTIONS_UPDATEFLAG  => Input::get(_Import::OPTIONS_UPDATEFLAG)
            ]);            
            
            $this->import->skip(1)->calculate(false)->chunk(_Import::CHUNK_SIZE, $this->getChunkCallback());

            $this->_removeDuplicate()->_saveTaskStatic();
        //});        

        return $this->task;
    }

    protected function createNewTask()
    {
        $task = new PosMemberImportTask;

        $task->user_id      = Auth::user()->id;
        $task->name         = Input::get('name');
        $task->distinction  = Input::get(_Import::OPTIONS_DISTINCTION);
        $task->category     = Input::get(_Import::OPTIONS_CATEGORY);
        $task->update_flags = Flater::getInflateFlag(Input::get(_Import::OPTIONS_INSERTFLAG));
        $task->insert_flags = Flater::getInflateFlag(Input::get(_Import::OPTIONS_UPDATEFLAG));
        $task->kind_id      = Input::get('kind_id');
        $task->memo         = Input::get(_Import::OPTIONS_OBMEMO);
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
        $content = $this->modelFactory->create($this->adapter);
        $content->save();

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