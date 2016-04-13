<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import\ImportPush;

use App\Import\Flap\POS_Member\Import;
use App\Model\Flap\PosMemberImportContent;
use App\Model\Flap\PosMemberImportTask;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\Flap\CCS_MemberFlags\Flater;
use App\Utility\Chinghwa\ORM\ERP\CCS_MemberFlags;
use App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler\Act\ModelFactory;

abstract class Pusher implements IPusher
{
    public function pushContent(PosMemberImportContent $content)
    {
        $start = microtime(true);

        $this->proc($content);            
        
        $executeTime = floor((microtime(true) - $start)*1000)/1000;

        return $executeTime;
    }

    public function pushTask(PosMemberImportTask $task)
    {
        $startTime = microtime(true);

        return $this->pushTaskDaemon($task)->taskUpdateProc($task, $startTime);
    }

    protected function pushTaskDaemon(PosMemberImportTask $task)
    {
        $contents = $task->content()->isNotExecuted()->take(Import::CHUNK_SIZE)->get();
        
        $contents->each(function ($content) {
            $this->proc($content);            
        });

        return 0 === $task->content()->isNotExecuted()->count() ? $this : $this->pushTaskDaemon($task);
    }

    protected function taskUpdateProc(PosMemberImportTask $task, $startTime)
    {
        $task->executed_at = new \DateTime();
        $task->execute_cost_time = floor(microtime(true) - $startTime);
        $task->save();

        return $task;
    }

    public function proc(PosMemberImportContent $content)
    {
        $content->setIsExist(ModelFactory::getExistOrNotByContent($content));

        return true === $content->is_exist ? $this->updateProc($content) : $this->insertProc($content);            
    }

    protected function contentUpdateProc(PosMemberImportContent $content)
    {
        $content->pushed_at = new \Carbon\Carbon();
        $content->status = $content->status|32;
        $content->save(); 

        return $this;
    }

    protected function memberflagUpdateProc(PosMemberImportContent $content)
    {
        Processor::execErp(CCS_MemberFlags::genUpdateFlagQueryByContent($content));

        return $this;
    }

    public function insertProc(PosMemberImportContent $content)
    {
        $row = array_get(Processor::getArrayResult('SELECT * FROM dbo.chinghwa_fnGetCMemberSerNoByTable()'), 0);
        
        list($content->serno, $content->code, $content->sernoi) = array_values($row);        
        
        Processor::execErp($this->getExecInsertSpQuery($content));

        return $this->memberflagUpdateProc($content)->contentUpdateProc($content);
    }

    protected function getInsertProcQuery(PosMemberImportContent $content){}

    protected function getWrapVal($val)
    {
        return Processor::getWrapVal($val);
    }

    public function updateProc(PosMemberImportContent $content)
    {
        Processor::execErp($this->getUpdateProcQuery($content));

        return $this->memberflagUpdateProc($content)->contentUpdateProc($content);
    }

    protected function getUpdateProcQuery(PosMemberImportContent $content){}
}