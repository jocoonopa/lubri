<?php

namespace App\Observer\PosMember;

use App\Import\Flap\POS_Member\Import;
use App\Model\Flap\PosMemberImportTask;
use App\Model\Flap\PosMemberImportContent;
use App\Utility\Chinghwa\Flap\POS_Member\Filter;

class LyinObserver
{
    protected $content;
    protected $filter;
    protected $distinction;
    protected $category;
    protected $task;

    public function updatingContent(PosMemberImportContent $content)
    {   
        $this->content = $content;        
        $this->filter = new Filter;

        $this
            ->fixCellphone()
            ->fixHometel()
            ->fixOfficetel()            
            ->fixFlag23WithPeriodAt()
            ->fixMemo()
            ->fixAddress()
            ->fixStatus()
            ->save()
        ;
    }

    public function updatedTask(PosMemberImportTask $task)
    {   
        $this->task = $task;
        $this->distinction = PosMemberImportTask::getBDSerNo($this->task->distinction);
        $this->category = PosMemberImportTask::getCategorySerNo($this->task->category);

        $this->task->content()->isNotExecuted()->chunk(Import::CHUNK_SIZE, function ($contents) {
            $contents->each(function ($content) {
                $content->flags       = $content->getFlags();
                $content->category    = $this->category;
                $content->distinction = $this->distinction;
                $content->save();
            });
        });        
    }

    protected function fixFlag23WithPeriodAt()
    {
        $this->content->fixFlag23WithPeriodAt();

        return $this;
    }

    protected function fixCellphone()
    {
        $this->content->cellphone = $this->filter->getCellphone($this->content->cellphone);

        return $this;
    }

    protected function fixHometel()
    {
        $this->content->hometel = $this->filter->getHometel($this->content->hometel);

        return $this;
    }

    protected function fixOfficetel()
    {
        $this->content->officetel = $this->filter->getOfficetel($this->content->officetel);

        return $this;
    }

    protected function fixMemo()
    {
        $this->content->memo = $this->content->genMemo();

        return $this;
    }

    protected function fixAddress()
    {
        $this->content->homeaddress = $this->filter->getAddress($this->content->state, $this->content->homeaddress);

        return $this;
    }

    protected function fixStatus()
    {
        $this->content->fixStatus();

        return $this;
    }

    protected function save()
    {
        $this->content->save();

        return $this;
    }
}