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
                Import::OPTIONS_DISTINCTION => Input::get(Import::OPTIONS_DISTINCTION),
                Import::OPTIONS_CATEGORY    => Input::get(Import::OPTIONS_CATEGORY),
                Import::OPTIONS_INSERTFLAG  => Input::get(Import::OPTIONS_INSERTFLAG),
                Import::OPTIONS_UPDATEFLAG  => Input::get(Import::OPTIONS_UPDATEFLAG)
            ]
        );

        $this->modelFactory = new ImportModelFactory;

        $this->task = new PosMemberImportTask;
        $this->task->user_id = Auth::user()->id;
        $this->task->save();

        $import->skip(1)->chunk(Import::CHUNK_SIZE, $this->getChunkCallback());
    }

    protected function getChunkCallback() {
        return function ($sheets) {
            $currentRowNum = 0;
            $error = [];

            foreach ($sheets[0] as $row){
                if (false === $this->adapter->inject($row)) {
                    $error[$currentRowNum] = json_encode($row);
                } else {
                    /**
                     * @var App\Model\Flap\PosMemberImportTaskContent
                     */
                    $content = $this->modelFactory->create($this->adapter);
                    $content->posmember_import_task_id = $this->task->id;
                    $content->save();
                }
                
                $currentRowNum ++;
            }

            $this->task->error = json_encode($error);
            $this->task->insert_count = PosMemberImportTaskContent::isNotExist($this->task->id);
            $this->task->update_count = PosMemberImportTaskContent::isExist($this->task->id);
            $this->task->save();
        };
    }

    /**
     * @deprecated 
     * @return array
     */
    protected function getImportHeadColumns()
    {
        return [
            '會員',
            '生日',
            '地址',
            '郵遞區號',
            '住家電話',
            '辦公電話',
            '行動電話',
            '預產期',
            'email',
            '生產醫院',
        ];
    }
} 