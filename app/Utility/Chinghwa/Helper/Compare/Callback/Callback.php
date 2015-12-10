<?php

namespace App\Utility\Chinghwa\Helper\Compare\Callback;

use App\Utility\Chinghwa\Helper\Compare\FixtureHelper;

class Callback
{
	protected $data;

    public function __construct($realPath)
    {
    	$this
    		->initData($realPath)
    		->setAppendInsertMemberProfileSheet()
    		->setAppendInsertMemberDistFlagSheet()
    		->setAppendUpdateMemberProfileSheet()
    		->setAppendUpdateMemberDistFlagSheet()
    	;
    }

    public function initData($realPath)
    {
    	$this->data = FixtureHelper::getDataPrototype($realPath);

    	return $this;
    }

    public function getData()
    {
    	return $this->data;
    }

    public function mergeData(array $src)
    {
    	$this->data = array_merge($this->data, $src);

    	return $this;
    }

    public function migrateData()
    {
        $this->data['iterateInsertTimes'] += count($this->data['insert']);
        $this->data['iterateUpdateTimes'] += count($this->data['update']);

        // release array memory
        unset($this->data['update']);
        unset($this->data['insert']);
        
        $this->data['update'] = [];
        $this->data['insert'] = [];         

        return $this;
    }

   	public function getAppendInsertClosureMemberProfile()
    {
        return $this->appendInsertClosureMemberProfile;
    }

    public function getAppendInsertClosureMemberDistFlag()
    {
        return $this->appendInsertClosureMemberDistFlag;
    }

    public function getAppendUpdateClosureMemberProfile()
    {
        return $this->appendUpdateClosureMemberProfile;
    }

    public function getAppendUpdateClosureMemberDistFlag()
    {
        return $this->appendUpdateClosureMemberDistFlag;
    }

	protected function setAppendInsertMemberProfileSheet()
    {
        $this->appendInsertClosureMemberProfile = function($sheet) {
            $this->appendRow($sheet, $this->data['insert'], $this->data['iterateInsertTimes'], 'memberinfo');
        };

        return $this;
    }

    protected function setAppendInsertMemberDistFlagSheet()
    {
        $this->appendInsertClosureMemberDistFlag = function($sheet) {
            $this->appendRow($sheet, $this->data['insert'], $this->data['iterateInsertTimes'], 'flag');
        };

        return $this;
    }

    protected function setAppendUpdateMemberProfileSheet()
    {
        $this->appendUpdateClosureMemberProfile = function($sheet) {
            $this->appendRow($sheet, $this->data['update'], $this->data['iterateUpdateTimes'], 'memberinfo');
        };

        return $this;
    }

    protected function setAppendUpdateMemberDistFlagSheet()
    {
        $this->appendUpdateClosureMemberDistFlag = function($sheet) {
            $this->appendRow($sheet, $this->data['update'], $this->data['iterateUpdateTimes'], 'flag');
        };

        return $this;
    }

    protected function appendRow(&$sheet, array $iuData, $point, $srcKey)
    {
        foreach ($iuData as $key => $info) {
            $sheet->appendRow($this->getAppendRowIndex($key, $point), $info[$srcKey]);                    
        }

        return $this;
    }

    /**
     * 取得目前新增 row 的正確 row index
     * 
     * @param  int    $key          [資料陣列的鍵]
     * @param  size_t $iterateTimes [chunk 的迭代次數]
     * @return int           
     */
    protected function getAppendRowIndex($key, $iterateTimes)
    {
        return $key + 2 + $iterateTimes;
    }
}