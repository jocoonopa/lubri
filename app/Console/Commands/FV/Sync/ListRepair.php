<?php

namespace App\Console\Commands\FV\Sync;

use App;
use App\Export\FV\Sync\Helper\ExecuteAgent;
use App\Export\FV\Sync\Helper\FileWriter\MemberFileWriter AS FileWriter;
use App\Export\FV\Sync\Helper\FileWriter\ListFileWriter;
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
    const FOLDER_INCOMING          = 'Incoming';
    const FOLDER_REJECT            = 'Reject';

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

    public function __construct(FileWriter $memberWriter, ListFileWriter $ctiWriter)
    {
        parent::__construct();

        $this->setMemberWriter($memberWriter)->setCtiWriter($ctiWriter);
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {        
        set_time_limit(0);

        $this->comment("\r\n|||||||||||| CallList Fix Proc Begin ||||||||||||\r\n");
        $this->proc();
        $this->comment("\r\n|||||||||||| CallList Fix Proc Finish ||||||||||||\r\n");    
    }

    protected function proc()
    {
        $ids = $this->parseLogAndGetCustIds($this->fetchQue());

        if (empty($ids)) {
            return $this->comment('No error need to be fixed!');
        }

        $members = $this->fetchMembers($ids);

        ExecuteAgent::command(FVSyncType::VIGATYPE_MEMBER, $this->genMemberFileAndGetPath($members));
        ExecuteAgent::command(FVSyncType::VIGATYPE_LIST, $this->getCtiWriter()->getFname());   
    }

    protected function fetchQue()
    {
        $que = FVSyncQue::find($this->option('id'));

        if (NULL === $que) {
            throw new \Exception("Not found with given id {$this->option('id')}");
        }

        if (FVSyncType::ID_LIST !== $que->type_id) {
            throw new \Exception('Unvalid Que type!');
        }

        return $que;
    }

    protected function parseLogAndGetCustIds(FVSyncQue $que)
    {
        try {
            $ids = [];

            $this->getCtiWriter()->refresh()->open();
            Excel::filter('chunk')->load($this->getErrorLogFilePath($que))->chunk($this->option('chunk'), $this->iterateProc($ids));
            $this->getCtiWriter()->close();

            return $ids;
        } catch (\Exception $e) {
            $this->comment($e->getMessage());
        }
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

    protected function getErrorLogFilePath(FVSyncQue $que)
    {
        return str_replace(
            [env('VIGA_INCOMING_DIR_NAME', self::FOLDER_INCOMING), basename($que->dest_file, self::FILE_EXTENSION)], 
            [env('VIGA_REJECT_DIR_NAME', self::FOLDER_REJECT), basename($que->dest_file, self::FILE_EXTENSION) . self::FILE_SUFFIX], 
            $que->dest_file
        );
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

    protected function genMemberFileAndGetPath(array $members)
    {
        $this->getMemberWriter()->write($members);

        $basename = basename($this->getMemberWriter()->getFname(), self::FILE_EXTENSION) . self::FILE_EXTENSION;
        $dest     = env('FVSYNC_MEMBER_STORAGE_PATH') . $basename;
        
        return rename($this->getMemberWriter()->getFname(), $dest) ? $dest : null;
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
