<?php

namespace App\Http\Controllers\Flap\POS_Member;

use App\Http\Controllers\Controller;
use App\Import\Flap\POS_Member\Import;
use App\Jobs\PushPosMemberTask;
use App\Model\Flap\PosMemberImportContent;
use App\Model\Flap\PosMemberImportTask;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler\Lyin\ModelFactory;
use Session;

class ImportPushController extends Controller
{
    public function push(PosMemberImportTask $task)
    {
        set_time_limit(0);

        $job = with(new PushPosMemberTask($task))->onQueue(env('IRON_QUEUE'))->delay(10);
        $this->dispatch($job);

        $task->status_code = PosMemberImportTask::STATUS_PUSHING;
        $task->save();

        Session::flash('success', "<b>{$task->name}<b> 推送中，完成後系統會發送通知信件.");

        return redirect("/flap/pos_member/import_task?kind_id={$task->kind()->first()->id}"); 
    }

    protected function getPusher(PosMemberImportTask $task)
    {
        $importKind = $task->kind()->first();

        $pushClass = $importKind->pusher;

        return with(new $pushClass);
    }

    /**
     * use chinghwa;
     * GO
     *
     * DECLARE @serNo VARCHAR(40)
     *
     * SET @serNo = 'MEMBR000000000000787392200'
     *
     * exec [dbo].[sp_DeleteHBMember] @serNo
     */ 
    public function pushone(PosMemberImportTask $task, PosMemberImportContent $content)
    {
        $start = microtime(true);

        $this->getPusher($task)->pushContent($content);       
        
        $executeTime = floor((microtime(true) - $start)*1000)/1000;

        Session::flash('success', "<b>{$task->name}<b> 的 <b>{$content->name}</b> 推送完成，費時 {$executeTime} 秒");

        return redirect("/flap/pos_member/import_task/{$task->id}"); 
    }

    /**
     * use chinghwa;
     * GO
     *
     * DECLARE @serNo VARCHAR(40)
     *
     * SET @serNo = 'MEMBR000000000000787392200'
     *
     * exec [dbo].[sp_DeleteHBMember] @serNo
     */ 
    public function pull(PosMemberImportTask $task)
    {
        set_time_limit(0);

        try {
            $task->updated_at = new \DateTime();
            $task->status_code = PosMemberImportTask::PULLING;
            $task->save();

            $task->content()->isNotExecuted()->chunk(Import::CHUNK_SIZE, function ($contents) {           
                $contents->each(function ($content) {
                    $content->setIsExist(ModelFactory::getExistOrNotByContent($content));
                    $content->save();           
                });
            });

            $task->updated_at = new \DateTime();
            $task->status_code = PosMemberImportTask::TOBEPUSHED;
            $task->save();

            Session::flash('success', "<b>{$task->name}<b> 與ERP資料同步完成!");
        } catch (\Exception $e) {
            $task->updated_at = new \DateTime();
            $task->status_code = PosMemberImportTask::TOBEPUSHED;
            $task->save();

            Session::flash('error', "<b>{$task->name}<b> {$e->getMessage()}");
        }
        
        return redirect("/flap/pos_member/import_task/{$task->id}"); 
    }

    public function rollback()
    {
        $members = Processor::getArrayResult("SELECT TOP 3000 * FROM POS_Member WITH(NOLOCK) WHERE Code LIKE 'T%' AND CRT_TIME>= '2016-02-18 00:00:00' ORDER BY SerNo DESC");

        foreach ($members as $member) {
            print("EXEC dbo.sp_DeleteHBMember '{$member['SerNo']}'<br/>");
        }

        return '<hr>請將畫面上的SQL語句貼在欲直行的資料庫進行還原~';
    }
}
