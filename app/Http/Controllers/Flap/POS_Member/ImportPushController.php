<?php

namespace App\Http\Controllers\Flap\POS_Member;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Model\Flap\PosMemberImportTask;
use App\Model\Flap\PosMemberImportTaskContent;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\Flap\POS_Member\Import\Import;
use App\Utility\Chinghwa\Flap\POS_Member\Import\ImportModelFactory;
use Auth;
use Illuminate\Http\Request;
use Input;
use Session;

class ImportPushController extends Controller
{
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
    public function push(PosMemberImportTask $task)
    {
        $start = microtime(true);

        $task->content()->isNotExecuted()->chunk(Import::CHUNK_SIZE, function ($contents) {            
            $contents->each(function ($content) {
                $this->_pushProcess($content);            
            });
        });
        
        $task->execute_cost_time = floor(microtime(true) - $start);
        $task->executed_at = new \DateTime();
        $task->save();

        Session::flash('success', "任務{$task->id}執行完成!，費時 {$task->execute_cost_time} 秒");

        return redirect()->action('Flap\POS_Member\ImportTaskController@index'); 
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
    public function pushone(PosMemberImportTask $task, PosMemberImportTaskContent $content)
    {
        $start = microtime(true);

        $this->_pushProcess($content);            
        
        $executeTime = floor((microtime(true) - $start)*1000)/1000;

        Session::flash('success', "任務{$task->id}的 <b>{$content->name}</b> 推送完成，費時 {$executeTime} 秒");

        return redirect("/flap/pos_member/import_task/{$task->id}/content"); 
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
        $task->content()->isNotExecuted()->chunk(Import::CHUNK_SIZE, function ($contents) {            
            $contents->each(function ($content) {
                $content->setIsExist(ImportModelFactory::getExistOrNotByContent($content));
                $content->save();           
            });
        });

        Session::flash('success', "任務{$task->id}與ERP資料同步完成!");

        return redirect("/flap/pos_member/import_task/{$task->id}/content"); 
    }

    public function rollback()
    {
        $members = Processor::getArrayResult("SELECT TOP 100 * FROM POS_Member WITH(NOLOCK) WHERE Code LIKE 'T%' AND CRT_TIME>= '2016-02-05 00:00:00' ORDER BY SerNo DESC");

        foreach ($members as $member) {
            echo "EXEC dbo.sp_DeleteHBMember '{$member['SerNo']}'<br/>";
        }

        dd($members);
    }

    protected function _pushProcess(PosMemberImportTaskContent $content)
    {
        $content->setIsExist(ImportModelFactory::getExistOrNotByContent($content));

        $content->isExist ? $this->_pushUpdateProcess($content) : $this->_pushInsertProcess($content);
            
        Processor::execErp($this->_getUpdateFlagQuery($content));
        
        $content->pushed_at = new \Carbon\Carbon();
        $content->status = $content->status|32;
        $content->save();   

        return $this;
    }

    private function _pushUpdateProcess(PosMemberImportTaskContent $content)
    {
        return Processor::execErp("UPDATE CCS_CRMFields SET CRMNote1='{$content->memo}' WHERE MemberSerNoStr = '{$content->serNo}'");
    }

    private function _pushInsertProcess(PosMemberImportTaskContent $content)
    {
        $row = array_get(Processor::getArrayResult('SELECT * FROM dbo.chinghwa_fnGetCMemberSerNoByTable()'), 0);
        list($serNo, $code, $serNoI) = array_values($row);
        
        $content->serNo = $serNo; 
        $content->code = $code; 
        $content->serNoI = $serNoI;
        
        return Processor::execErp($this->_getExecSpQuery($content));
    }

    /** ALTER PROCEDURE [dbo].[sp_InsertHBMember]
     *     @serNo,
     *     @code,
     *     @serNoI,
     *     @name varchar(20)      = NULL,
     *     @sex varchar(2)        = '0',
     *     @birthday varchar(15)  = NULL,
     *     @homeTel varchar(20)   = NULL,
     *     @officeTel varchar(20) = NULL,
     *     @cellPhone varchar(20) = NULL,
     *     @email varchar(60)     = NULL,
     *     @zipcode varchar(6)    = '000',
     *     @city varchar(30)      = '',
     *     @state varchar(30)     = '',
     *     @address varchar(100)  = '',
     *     @memberClassSerNo varchar(40),
     *     @salePointSerNo varchar(40),
     *     @employeeSerNo varchar(40),
     *     @exploitSerNo integer,
     *     @exploitEmpSerNo varchar(40),
     *     @distinction integer,
     *     @memberLevelEC integer,
     *     @employCode varchar(40)
     */
    private function _getExecSpQuery(PosMemberImportTaskContent $content)
    {
        $sql = "exec dbo.sp_InsertHBMember ";
        $sql.= "{$this->_getPropertyOrNull($content->serNo)}";
        $sql.= ", {$this->_getPropertyOrNull($content->code)}";
        $sql.= ", {$this->_getPropertyOrNull($content->serNoI)}";
        $sql.= ",{$this->_getPropertyOrNull($content->name)},";
        $sql.= ("'female'" === $this->_getPropertyOrNull($content->sex)) ? '0' : '1';
        $sql.= ",{$this->_getPropertyOrNull($content->birthday)}";
        $sql.= ",{$this->_getPropertyOrNull($content->hometel)}";
        $sql.= ",{$this->_getPropertyOrNull($content->officetel)}";
        $sql.= ",{$this->_getPropertyOrNull($content->cellphone)}";
        $sql.= ",{$this->_getPropertyOrNull($content->email)}";
        $sql.= ",{$this->_getPropertyOrNull($content->zipcode)}";
        $sql.= ",{$this->_getPropertyOrNull($content->city)}";
        $sql.= ",{$this->_getPropertyOrNull($content->state)}";
        $sql.= ",{$this->_getPropertyOrNull($content->homeaddress)}";
        $sql.= ",{$this->_getPropertyOrNull($content->category)}";
        $sql.= ",{$this->_getPropertyOrNull($content->salepoint_serno)}";
        $sql.= ",{$this->_getPropertyOrNull($content->employee_serno)}";
        $sql.= ",{$this->_getPropertyOrNull($content->exploit_serno)}";
        $sql.= ",{$this->_getPropertyOrNull($content->exploit_emp_serno)}";
        $sql.= ",{$this->_getPropertyOrNull($content->distinction)}";
        $sql.= ",{$this->_getPropertyOrNull($content->member_level_ec)}";
        $sql.= ",{$this->_getPropertyOrNull($content->employCode)}";
        $sql.= ",{$this->_getPropertyOrNull($content->memo)}";

        return $sql;
    }

    private function _getPropertyOrNull($val)
    {
        return empty($val) ? 'NULL' : "'{$val}'";
    }

    private function _getUpdateFlagQuery(PosMemberImportTaskContent $content)
    {
        $sql = 'UPDATE CCS_MemberFlags SET ';

        foreach (json_decode($content->flags, true) as $key => $flag) {
            $sql.= "Distflags_{$key}='{$flag}',";
        }

        return substr($sql, 0, -1) . " WHERE MemberSerNoStr='$content->serNo'";
    }
}
