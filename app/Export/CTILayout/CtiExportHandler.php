<?php

namespace App\Export\CTILayout;

use App;
use App\Export\CTILayout\CtiExportCriteria;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Input;

/**
 * @deprecated [<20160712>] [<No more need to use this class>]
 */
class CtiExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
    protected $engOptions;
    protected $criteria;
    protected $writer;

    public function handle($export)
    {
        $this->initEndOptions()->initCriteria()->initWriter();
        $this->getWriter()->write($this->fetch());

        return $export->setFile($this->getWriter()->getFname());
    }

    protected function fetch()
    {
        $whereStr = $this->getCriteria()->apply($this->getEngOptions())->getWhereStr();

        return Processor::getArrayResult($this->genAndGetSql($whereStr), Processor::DB_CTI);
    }

    protected function genAndGetSql($whereStr)
    {
        return str_replace('$whereStr', $whereStr, Processor::getStorageSql('CTILayout_ENG.sql'));
    }

    protected function initWriter()
    {
        return $this->setWriter(App::make('App\Export\FV\Sync\ListFileWriter'));
    }

    protected function initEndOptions()
    {
        return $this->setEngOptions([
            'agentCD'    => Input::get('eng_emp_codes', []),
            'sourceCD'   => Input::get('eng_source_cds', []),
            'campaignCD' => Input::get('eng_campaign_cds', []),
            'assignDate' => trim(Input::get('eng_assign_date'))
        ]);
    }

    protected function initCriteria()
    {
        return $this->setCriteria(new CtiExportCriteria);
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
     * Gets the value of criteria.
     *
     * @return mixed
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * Sets the value of criteria.
     *
     * @param mixed $criteria the criteria
     *
     * @return self
     */
    protected function setCriteria($criteria)
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * Gets the value of writer.
     *
     * @return mixed
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * Sets the value of writer.
     *
     * @param mixed $writer the writer
     *
     * @return self
     */
    protected function setWriter($writer)
    {
        $this->writer = $writer;

        return $this;
    }
}