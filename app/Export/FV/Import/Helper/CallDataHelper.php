<?php

namespace App\Export\FV\Import\Helper;

use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\Helper\Flap\PosMember\MemberCode;
use Carbon\Carbon;
use Log;

/**
 * Help ExportHandler deal with data from flap, cti
 */
class CallDataHelper extends DataHelper
{
    public $targetCount = NULL;

    /**
     * @override
     * @return $this
     */
    protected function initCount()
    {
        return $this->setTargetCount($this->fetchTargetCount());
    }

    protected function fetchLists($i)
    { 
        $placeholder = ['$sourcecds', '$mdtTime', '$begin', '$end'];
        $replace = [sqlInWrap($this->getCondition()['targets']), $this->getCondition()['mdtTime'], $i, $i + $this->getChunkSize()];

        $sql = str_replace($placeholder, $replace, Processor::getStorageSql('FV/Import/list.sql'));

        return Processor::getArrayResult($sql, Processor::DB_CTI);
    }

    protected function fetchListsCount()
    {
        $sql = str_replace(['$mdtTime', '$sourcecds'], [$this->getCondition()['mdtTime'], sqlInWrap($this->getCondition()['targets'])], Processor::getStorageSql('FV/Import/list_count.sql'));

        return array_get(Processor::getArrayResult($sql, Processor::DB_CTI), 0)['_count'];
    }

    protected function fetchCalllogs($i)
    {
        $placeholder = ['$codes', '$mdtTime', '$begin', '$end'];
        $replace = [sqlInWrap($this->getCondition()['targets']), $this->getCondition()['mdtTime'], $i, $i + $this->getChunkSize()];

        $sql = str_replace($placeholder, $replace, Processor::getStorageSql('FV/Import/calllog.sql'));

        return Processor::getArrayResult($sql, Processor::DB_CTI);
    }

    protected function fetchCalllogsCount()
    {
        $sql = str_replace(
            ['$mdtTime', '$codes'], 
            [$this->getCondition()['mdtTime'], sqlInWrap($this->getCondition()['targets'])], 
            Processor::getStorageSql('FV/Import/calllog_count.sql')
        );

        return array_get(Processor::getArrayResult($sql, Processor::DB_CTI), 0)['_count'];
    }

    public function updateTarget($i, $chunkSize)
    {
        $sql = str_replace(
            ['$date', '$serno', '$begin', '$end'], 
            [$this->_getSpecificDateStr(), MemberCode::genSerNoStr(1), $i, $i + $chunkSize], 
            Processor::getStorageSql('FV/Import/list_and_log_target.sql')
        );

        $this->condition['targets'] = array_pluck(Processor::getArrayResult($sql), 'Code');

        return $this;
    }

    public function fetchTargetCount()
    {
        $sql = str_replace(
            ['$date', '$serno'], 
            [$this->_getSpecificDateStr(), MemberCode::genSerNoStr(1)], 
            Processor::getStorageSql('FV/Import/list_and_log_target_count.sql')
        );

        return array_get(Processor::getArrayResult($sql), 0)['_count'];
    }

    private function _getSpecificDateStr()
    {
        return Carbon::now()->subYears(2)->modify('first day of january')->format('Y-m-d H:i:s');
    }

    /**
     * Gets the value of targetCount.
     *
     * @return mixed
     */
    public function getTargetCount()
    {
        return $this->targetCount;
    }

    /**
     * Sets the value of targetCount.
     *
     * @param mixed $targetCount the target count
     *
     * @return self
     */
    public function setTargetCount($targetCount)
    {
        $this->targetCount = $targetCount;

        return $this;
    }
}