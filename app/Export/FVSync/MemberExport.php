<?php

namespace App\Export\FVSync;

use App\Export\Mould\FVMemberMould;
use Carbon\Carbon;

class MemberExport extends FVSyncExport
{
    public function getMould()
    {
        return new FVMemberMould;
    }

    /**
     * Decide which type of que would be use, must override this constant
     */
    public function getQueType()
    {
        return 'member';
    }

    /**
     * The fetch start date, must override this constant
     */
    public function getStartDate()
    {
        return '2016-06-14 00:00:00';
    }

    public function setQueStatus($statusCode)
    {
        $this->getQue()->status_code = $statusCode;
        $this->getQue()->save();

        return $this;
    }

    public function configQue()
    {
        $this->que->import_cost_time = $this->getImportCostTime();
        $this->que->select_cost_time = $this->getSelectCostTime();
        $this->que->dest_file        = $this->getInfo()['file'];
        $this->que->last_modified_at = $this->que->created_at;
        $this->que->save();

        return $this;
    }
}