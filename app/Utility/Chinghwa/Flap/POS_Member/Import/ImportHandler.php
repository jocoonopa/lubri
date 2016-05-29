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
use Excel;
use Input;

class ImportHandler implements \Maatwebsite\Excel\Files\ImportHandler 
{
    /**
     * $this->import->skip(1) is type of \Maatwebsite\Excel\Readers\LaravelExcelReader
     */
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

        try {
            // $this->import->skip(1)->calculate(false)->chunk(_Import::CHUNK_SIZE, $this->getChunkCallback()); 
            // 無法處理多個 sheets 的 bug，因此改為下面的方式處理:
            //======================================================//                                   
            // 取得上傳暫存檔路徑
            $filePath = $this->import->skip(0)->file;

            // 透過直接指定選擇第一個sheet的方式，繞過 chunk 的 bug
            $reader = Excel::filter('chunk')
                ->selectSheetsByIndex(0)
                ->load($filePath)
                ->skip(1);

            $totalRows = $reader->getTotalRowsOfFile();

            
            //======================================================//
            
            $this->task   = $this->createNewTask($totalRows);
            $kind         = $this->task->kind()->first();            
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
            
            $reader->chunk(_Import::CHUNK_SIZE, $this->getChunkCallback());

            $this->_removeDuplicate()->_saveTaskStatic();
        } catch (\Exception $e) {
            $this->task->delete();
        }
        return $this->task;
    }

    protected function createNewTask($totalRows = 0)
    {
        $task = new PosMemberImportTask;

        $task->user_id      = Auth::user()->id;
        $task->name         = Input::get('name');
        $task->status_code  = PosMemberImportTask::STATUS_IMPORTING;
        $task->total_count  = $totalRows;
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