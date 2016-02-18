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
     * 1. Check import columns 
     * 2. Iterate results
     *     2-1. Valid each column
     *     2-2. Convert each column data and compose it into our table format
     *     2-3. Post data to 66
     *         2-3-1. Check whether is Insert process or Update process(return {serNo, code}, if not exist, return empty)
     *             2-3-1-i-1. 
     *                 -- Fetch the last POS_Member.SerNo by cursor
     *                     SET @serNo = dbo.chinghwa_fnGetNewMemberSerNo()
     *
     *                 -- Fetch the last T Code of POS_Member by cursor
     *                     SET @code = dbo.chinghwa_fnGetNewMemberTCode()
     *
     *                 -- Fetch the last POS_Member.MemberSerNoI by cursor
     *                     SET @serNoI = dbo.chinghwa_fnGetNewMemberSerNoI()
     *             2-3-1-i-2.
     *                 Exec sp_InsertHBMember
     *                  
     *             2-3-1-u-1. 
     *                 Exex CCS_CRMFields[CRMNote1, newCustomerMemo] update and MemberFlag update
     *                 
     *     2-4. Update result to our table row
     * 3. Iterate results again
     *     3-1. Get each member's serNo                                                                                                                                                         , Code
     *     3-2. Update to our table row
     * 4. Send nofification to those who need to know, the notification is a link to task detail page
     * 5. View display task result detail, has pagination, order feature
     * 
     * @param  $import
     * @return mixed
     */
    public function handle($import)
    {
        $this->adapter = new ImportColumnAdapter(
            [
                Import::OPTIONS_DISTINCTION => $this->_getBDSerNo(Input::get(Import::OPTIONS_DISTINCTION)),
                Import::OPTIONS_CATEGORY    => $this->_getCategorySerNo(Input::get(Import::OPTIONS_CATEGORY)),
                Import::OPTIONS_INSERTFLAG  => Input::get(Import::OPTIONS_INSERTFLAG),
                Import::OPTIONS_UPDATEFLAG  => Input::get(Import::OPTIONS_UPDATEFLAG)
            ]
        );

        $this->modelFactory = new ImportModelFactory;
        $this->task = $this->createNewTask();

        $import->calculate(false)->chunk(Import::CHUNK_SIZE, $this->getChunkCallback());

        return $this->_removeDuplicate()->_saveTaskStatic();
    }

    protected function createNewTask()
    {
        $task = new PosMemberImportTask;
        $task->user_id = Auth::user()->id;
        $task->update_flags = json_encode($this->adapter->getUpdateFlagPairs());
        $task->insert_flags = json_encode($this->adapter->getInsertFlagPairs());
        $task->save();

        return $task;
    }

    private function _getBDSerNo($str)
    {
        $q = Processor::table('BasicDataDef')
            ->select('TOP 1 BDSerNo')
            ->where('BDCode', '=', $str)
        ;

        return array_get(Processor::getArrayResult($q), '0.BDSerNo');
    }

    private function _getCategorySerNo($str)
    {
        $q = Processor::table('POS_MemberCategory')
            ->select('TOP 1 SerNo')
            ->where('Code', '=', $str)
        ;

        return array_get(Processor::getArrayResult($q), '0.SerNo');
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
        /**
         * @var App\Model\Flap\PosMemberImportTaskContent
         */
        $content = $this->modelFactory->create($this->adapter);
        $content->pos_member_import_task_id = $this->task->id;
        $content->save();

        return $this;
    }

    private function _removeDuplicate()
    {
        $colNames = ['cellphone', 'hometel', 'homeaddress'];

        foreach ($colNames as $colName) {
            $duplicateContents = $this->task->content()->isDuplicate($colName)->get();

            foreach ($duplicateContents as $duplicateContent) {
                $this->task->content()->duplicateWithThis($colName, $duplicateContent)->get()->each(function ($toBeRemoveContent) {
                    $toBeRemoveContent->delete();
                });
            }
        }

        return $this;
    }

    private function _saveTaskStatic()
    {
        $this->task->error = json_encode($this->error);
        $this->task->updateStat()->save();

        return $this->task;
    }
} 