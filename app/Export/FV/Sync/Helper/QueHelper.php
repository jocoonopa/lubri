<?php

namespace App\Export\FV\Sync\Helper;

use App\Model\Log\FVSyncQue;
use App\Model\Log\FVSyncType;
use Carbon\Carbon;

/**
 * Help ExportHandler deal que operate
 */
class QueHelper
{
    protected $export;
    protected $que;
    protected $type;
    protected $lastMrtTime;
    protected $importCostTime;
    protected $selectCostTime;
    protected $dependLimitTime;

    public function __construct($export)
    {
        $this->export = $export;
        $this->setType($export->getType())->createQue()->initLastMrtTime();

        $this->setDependLimitTime($this->fetchDependLimitTimeByQue());
    }

    protected function createQue()
    {
        $this->setQue(new FVSyncQue);

        $this->que->status_code = FVSyncQue::STATUS_INIT;
        $this->que->type_id = FVSyncType::where('name', '=', $this->getType())->first()->id;

        $this->que->save();

        return $this;
    }

    protected function initLastMrtTime()
    {
        $selfLastQue = FVSyncQue::latest()
            ->where('type_id', '=', FVSyncType::where('name', '=', $this->getType())->first()->id)
            ->whereNotNull('last_modified_at')
            ->where('status_code', '=', FVSyncQue::STATUS_COMPLETE)
            ->first();
        
        return $this->setLastMrtTime(!$selfLastQue ? Carbon::instance(with(new \DateTime($this->export->getStartDate()))) : $selfLastQue->last_modified_at);
    }

    public function getDependLimitTime()
    {
        return $this->dependLimitTime;
    }

    protected function fetchDependLimitTimeByQue()
    {
        $dependQue =  FVSyncQue::latest()
            ->where('type_id', '=', FVSyncType::where('id', '=', $this->que->type->depend_on_id)->first()->id)
            ->whereNotNull('last_modified_at')
            ->where('status_code', '=', FVSyncQue::STATUS_COMPLETE)
            ->first();

         return $dependQue->last_modified_at; 
    }

    protected function setDependLimitTime($dependLimitTime)
    {
        $this->dependLimitTime = $dependLimitTime;

        return $this;
    }

    public function hasProcessingQue()
    {
        $num = FVSyncQue::where('type_id', '=', FVSyncType::where('name', '=', $this->getType())->first()->id)
            ->whereIn('status_code', [FVSyncQue::STATUS_WRITING, FVSyncQue::STATUS_IMPORTING])
            ->count();

        return 0 < $num;
    }

    public function toWritingStatus()
    {
        return $this->setQueStatus(FVSyncQue::STATUS_WRITING)->configQue();
    }

    public function toImportingStatus()
    {
        return $this->setQueStatus(FVSyncQue::STATUS_IMPORTING)->configQue();
    }

    public function toCompleteStatus()
    {
        return $this->setQueStatus(FVSyncQue::STATUS_COMPLETE)->configQue();
    }

    public function toSkipStatus()
    {
        return $this->setQueStatus(FVSyncQue::STATUS_SKIP);
    }

    public function toErrorStatus()
    {
        return $this->setQueStatus(FVSyncQue::STATUS_EXCEPTION)->configQue();
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
        $this->que->dest_file        = $this->export->getInfo()['file'];
        $this->que->last_modified_at = ($this->que->created_at->gt($this->getDependLimitTime())) ? $this->getDependLimitTime() : $this->que->created_at;
        $this->que->save();

        return $this;
    }

    /**
     * Gets the value of que.
     *
     * @return mixed
     */
    public function getQue()
    {
        return $this->que;
    }

    /**
     * Sets the value of que.
     *
     * @param mixed $que the que
     *
     * @return self
     */
    public function setQue($que)
    {
        $this->que = $que;

        return $this;
    }

    /**
     * Gets the value of type.
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the value of type.
     *
     * @param mixed $type the type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Gets the value of lastMrtTime.
     *
     * @return mixed
     */
    public function getLastMrtTime()
    {
        return $this->lastMrtTime;
    }

    /**
     * Sets the value of lastMrtTime.
     *
     * @param mixed $lastMrtTime the last mrt time
     *
     * @return self
     */
    public function setLastMrtTime($lastMrtTime)
    {
        $this->lastMrtTime = $lastMrtTime;

        return $this;
    }

    /**
     * Gets the value of importCostTime.
     *
     * @return mixed
     */
    public function getImportCostTime()
    {
        return $this->importCostTime;
    }

    /**
     * Sets the value of importCostTime.
     *
     * @param mixed $importCostTime the import cost time
     *
     * @return self
     */
    public function setImportCostTime($importCostTime)
    {
        $this->importCostTime = $importCostTime;

        return $this;
    }

    /**
     * Gets the value of selectCostTime.
     *
     * @return mixed
     */
    public function getSelectCostTime()
    {
        return $this->selectCostTime;
    }

    /**
     * Sets the value of selectCostTime.
     *
     * @param mixed $selectCostTime the select cost time
     *
     * @return self
     */
    public function setSelectCostTime($selectCostTime)
    {
        $this->selectCostTime = $selectCostTime;

        return $this;
    }
}