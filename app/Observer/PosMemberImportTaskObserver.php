<?php

namespace App\Observer;

use App\Model\Flap\PosMemberImportTask;

class PosMemberImportTaskObserver 
{
    public function updated(PosMemberImportTask $task)
    {   
        $className = $task->kind()->first()->observer;
        
        return with(new $className)->updatedTask($task);   
    }
}