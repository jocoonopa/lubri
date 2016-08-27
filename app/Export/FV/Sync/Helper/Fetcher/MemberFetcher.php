<?php

namespace App\Export\FV\Sync\Helper\Fetcher;

use App\Export\CTILayout\CtiExportCriteria;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\ORM\CTI\CampaignCallList;

class MemberFetcher extends Fetcher
{
    const CHUNK_SIZE       = 50;
    const LIST_COUNT_LIMIT = 5000;

    protected $engOptions;
    protected $flapOptions;

    public function get(array $options)
    {
        if ($this->isValidOption($options)) {
            return $this
                ->setEngOptions(array_get($options, 'eng', []))
                ->setFlapOptions(array_get($options, 'flap', []))
                ->fetchFlapData($this->fetchCallLists())
            ;
        }
            
        return [];
    }

    protected function isValidOption(array $options)
    {
        return !empty($options);
    }

    protected function fetchCallLists()
    {
        if ($this->isIgnoreEngCondition($this->getEngOptions())) {
            return [];
        }

        if ($this->hasOverLimitCount($this->getEngOptions())) {
            throw new \Exception('瑛聲名單數目過多(超過' . self::LIST_COUNT_LIMIT . '筆)，請重新設定查詢條件!');
        }      
        
        return CampaignCallList::fetchCtiRes($this->getEngOptions());
    }

    protected function hasOverLimitCount(array $engOptions)
    {
        return self::LIST_COUNT_LIMIT < CampaignCallList::fetchCtiResCount($engOptions);
    }

    protected function isIgnoreEngCondition(array $engOptions)
    {
        foreach ($engOptions as $eachCondition) {
            if (empty($eachCondition)) {
                continue;
            }

            return false;
        }

        return true;
    }

    protected function fetchFlapData(array $callLists)
    {
        $memberCodes = array_pluck($callLists, 'SourceCD');
        $memberCodes = array_merge(array_get($this->getFlapOptions(), 'memberCodes', []), $memberCodes);

        return $this->filled($this->fetchMemberCodes(array_get($this->getFlapOptions(), 'empCodes', []), $memberCodes)); 
    }

    protected function fetchMemberCodes($empCodes, $memberCodes)
    {
        return empty($empCodes) ? $memberCodes : array_merge(array_pluck(Processor::getArrayResult($this->genFetchMemberCodesQ($empCodes)), 'cust_id'), $memberCodes);
    }

    protected function genFetchMemberCodesQ($empCodes)
    {
        return Processor::table('Customer_lubri')->whereIn('emp_id', $empCodes);
    }

    protected function filled(array $memberCodes)
    {
        $data = [];

        foreach (array_chunk($memberCodes, self::CHUNK_SIZE) as $chunk) {            
            foreach ($this->fetchMembers($chunk) as $member) {
                $data[] = $member;
            }   
        }

        return $data;
    }

    protected function fetchMembers(array $chunk)
    {
        return Processor::getArrayResult(str_replace('$memberCode', sqlInWrap($chunk), Processor::getStorageSql('CTILayout.sql')));
    }

    protected function toStringMember(array $member)
    {
        return implode(',', $this->getMould()->getRow($member)) . "\r\n";
    }    

    /**
     * Gets the value of engOptions.
     *
     * @return mixed
     */
    public function getEngOptions()
    {
        return $this->engOptions;
    }

    /**
     * Sets the value of engOptions.
     *
     * @param mixed $engOptions the eng options
     *
     * @return self
     */
    protected function setEngOptions($engOptions)
    {
        $this->engOptions = $engOptions;

        return $this;
    }

    /**
     * Gets the value of flapOptions.
     *
     * @return mixed
     */
    public function getFlapOptions()
    {
        return $this->flapOptions;
    }

    /**
     * Sets the value of flapOptions.
     *
     * @param mixed $flapOptions the flap options
     *
     * @return self
     */
    protected function setFlapOptions($flapOptions)
    {
        $this->flapOptions = $flapOptions;

        return $this;
    }
}