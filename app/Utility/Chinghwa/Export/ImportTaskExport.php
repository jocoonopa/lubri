<?php

namespace App\Utility\Chinghwa\Export;

use App\Model\Flap\PosMemberImportTask;

class ImportTaskExport extends \Maatwebsite\Excel\Files\NewExcelFile 
{
    protected $task;

    public function getFilename()
    {
        return "ImportTaskExport_" . time();
    }

    /**
     * Gets the value of task.
     *
     * @return mixed
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Sets the value of task.
     *
     * @param mixed $task the task
     *
     * @return self
     */
    public function setTask(PosMemberImportTask $task)
    {
        $this->task = $task;

        return $this;
    }
}