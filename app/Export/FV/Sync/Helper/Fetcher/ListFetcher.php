<?php

namespace App\Export\FV\Sync\Helper\Fetcher;

use App\Export\FV\Sync\Helper\Criteria\EngCriteria;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;

class ListFetcher extends Fetcher
{
    public function __construct()
    {
        $this->initCriteria();
    }

    public function get(array $options)
    {
        return $this->isValidCondition($options) ? $this->setOptions($options)->fetchEng() : [];
    }

    protected function isValidCondition(array $options)
    {
        return !empty($options);
    }

    protected function fetchEng()
    {
        $whereStr = $this->getCriteria()->apply($this->getOptions())->getWhereStr();

        return Processor::getArrayResult($this->genAndGetEngSql($whereStr), Processor::DB_CTI);
    }

    protected function genAndGetEngSql($whereStr)
    {
        return str_replace('$whereStr', $whereStr, Processor::getStorageSql('CTILayout_ENG.sql'));
    }

    protected function initCriteria()
    {
        return $this->setCriteria(new EngCriteria);
    }
}