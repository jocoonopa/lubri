<?php

namespace App\Observer;

use App\Model\Flap\PosMemberImportTask;
use App\Utility\Chinghwa\Flap\POS_Member\Import\Import;

class PosMemberImportTaskObserver 
{
    protected $distinction;
    protected $category;
    protected $task;

    public function updated(PosMemberImportTask $task)
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

}