<?php

namespace App\Console\Commands\FV\Sync;

use App;
use App\Export\FV\Sync\Helper\ExecuteAgent;
use App\Export\FV\Sync\MemberFileWriter AS FileWriter;
use App\Export\CTILayout\CtiExportFileWriter;
use App\Model\Log\FVSyncQue;
use App\Model\Log\FVSyncType;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Excel;
use Illuminate\Console\Command;
use Log;

class ListRepair extends Command
{
    const NOT_FOUND_MEMBER_MESSAGE = '沒有相關客戶資料';
    const ARRAY_INDEX_CUSTID       = 14;
    const FILE_EXTENSION           = '.csv';
    const FILE_SUFFIX              = '-Reject';
    const MAX_LIMIT_ROWS           = 5000;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fv:listrep {--id=0} {--chunk=250}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To repair the error part of SyncQue';

    protected $memberWriter;
    protected $ctiWriter;

    public function __construct(FileWriter $memberWriter, CtiExportFileWriter $ctiWriter)
    {
        parent::__construct();

        $this->setMemberWriter($memberWriter)->setCtiWriter($ctiWriter);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {        
        set_time_limit(0);

        $this->comment("\r\n|||||||||||| CallList Fix Proc Begin ||||||||||||\r\n");

        $ids = $this->parseLogAndGetCustIds($this->fetchQue());
        $members = $this->fetchMembers($ids);

        ExecuteAgent::command(FVSyncType::VIGATYPE_MEMBER, $this->genMemberFile($members));
        ExecuteAgent::command(FVSyncType::VIGATYPE_LIST, $this->getCtiWriter()->getFname());   

        $this->comment("\r\n|||||||||||| CallList Fix Proc Finish ||||||||||||\r\n");    
    }

    protected function fetchQue()
    {
        $que = FVSyncQue::find($this->option('id'));//->where('type_id', FVSyncType::ID_LIST)

        if (NULL === $que) {
            throw new \Exception("Not found with given id {$this->option('id')}");
        }

        if (FVSyncType::ID_LIST !== $que->type_id) {
            throw new \Exception('Unvalid Que type!');
        }

        return $que;
    }

    /**
     * Reject/xxxxx-Rejec.csv
     * @return [type] [description]
     */
    protected function parseLogAndGetCustIds(FVSyncQue $que)
    {
        $ids = [];

        $this->getCtiWriter()->open();

        Excel::filter('chunk')->load($this->getErrorLogFilePath($que))->chunk($this->option('chunk'), $this->iterateProc($ids));

        $this->getCtiWriter()->close();

        return $ids;
    }

    protected function iterateProc(&$ids)
    {
        return function($results) use (&$ids) {
            foreach($results as $row) {
                if (self::NOT_FOUND_MEMBER_MESSAGE === array_get($row, 0)) {
                    $ids[] = array_get($row, self::ARRAY_INDEX_CUSTID);

                    $this->getCtiWriter()->put($row->slice(1)->implode(',') . "\r\n");
                }
            }
        };
    }

    /**
     * Not implement yet
     */
    protected function getErrorLogFilePath(FVSyncQue $que)
    {
        return 'C:\FlapSync\CTI\Reject\reject.csv';
    }

    protected function fetchMembers(array $ids)
    {
        $whereCondition = 'WHERE POS_Member.Code IN(' . sqlInWrap($ids) . ')';

        return Processor::getArrayResult(str_replace([
            '$whereCondition', '$begin', '$end'], 
            [$whereCondition, 0, self::MAX_LIMIT_ROWS], 
            Processor::getStorageSql('FV/Import/member.sql')
        ));
    }

    protected function genMemberFile(array $members)
    {
        $this->getMemberWriter()->write($members);
        $basename = basename($this->getMemberWriter()->getFname(), self::FILE_EXTENSION) . self::FILE_EXTENSION;
        $dest = env('FVSYNC_MEMBER_STORAGE_PATH') . $basename;
        
        return copy($this->getMemberWriter()->getFname(), $dest) ? $dest : null;
    }

    /**
     * Gets the value of ctiWriter.
     *
     * @return mixed
     */
    public function getCtiWriter()
    {
        return $this->ctiWriter;
    }

    /**
     * Sets the value of ctiWriter.
     *
     * @param mixed $ctiWriter the cti writer
     *
     * @return self
     */
    protected function setCtiWriter($ctiWriter)
    {
        $this->ctiWriter = $ctiWriter;

        return $this;
    }

    /**
     * Gets the value of memberWriter.
     *
     * @return mixed
     */
    public function getMemberWriter()
    {
        return $this->memberWriter;
    }

    /**
     * Sets the value of memberWriter.
     *
     * @param mixed $memberWriter the member writer
     *
     * @return self
     */
    protected function setMemberWriter($memberWriter)
    {
        $this->memberWriter = $memberWriter;

        return $this;
    }
}
