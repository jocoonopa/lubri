<?php

namespace App\Export\FV\Sync\Helper;

use App\Export\FV\Helper\DataHelper AS DH;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;

/**
 * Help ExportHandler deal with data from flap, cti
 */
class DataHelper extends DH
{
    protected $mdtTime;

    public function __construct($type, $mdtTime, $chunkSize)
    {
        $this->setType($type)->setMdtTime($mdtTime)->setChunkSize($chunkSize)->initCount();
    }

    protected function fetchEntitysImplement($i, $flag = 'Erp')
    {
        $sql = str_replace(
            ['$mdtTime', '$begin', '$end'], 
            [$this->mdtTime->format('Y-m-d H:i:s'), $i, $i + $this->getChunkSize()], 
            Processor::getStorageSql("FV/Sync/{$this->type}.sql")
        );

        return Processor::getArrayResult($sql);
    }

    protected function fetchEntitysCountImplement($flag = 'Erp')
    {
        $sql = str_replace(
            ['$mdtTime'], 
            [$this->mdtTime->format('Y-m-d H:i:s')], 
            Processor::getStorageSql("FV/Sync/{$this->type}_count.sql")
        );

        return array_get(Processor::getArrayResult($sql, $flag), 0)['_count'];
    }

    protected function fetchMembers($i)
    {
        if ('member' !== $this->type) {
            throw new \Exception(__METHOD__ . " found exception! {$this->type} givend!");
        }

        return $this->fetchEntitysImplement($i);
    }

    protected function fetchOrders($i)
    {
        if ('order' !== $this->type) {
            throw new \Exception(__METHOD__ . " found exception! {$this->type} givend!");
        }

        return $this->fetchEntitysImplement($i);
    }

    protected function fetchProducts($i)
    {
        if ('product' !== $this->type) {
            throw new \Exception(__METHOD__ . " found exception! {$this->type} givend!");
        }

        return $this->fetchEntitysImplement($i);
    }

    protected function fetchCampaigns($i)
    {
        if ('campaign' !== $this->type) {
            throw new \Exception(__METHOD__ . " found exception! {$this->type} givend!");
        }

        return $this->fetchEntitysImplement($i, 'Cti');
    }

    protected function fetchCTIRecords($i)
    {
        if ('ctirecord' !== $this->type) {
            throw new \Exception(__METHOD__ . " found exception! {$this->type} givend!");
        }

        return $this->fetchEntitysImplement($i, 'Cti');
    }

    protected function fetchMembersCount()
    {
        if ('member' !== $this->type) {
            throw new \Exception(__METHOD__ . " found exception! {$this->type} givend!");
        }

        return $this->fetchEntitysCountImplement();
    }

    protected function fetchOrdersCount()
    {
        if ('order' !== $this->type) {
            throw new \Exception(__METHOD__ . " found exception! {$this->type} givend!");
        }

        return $this->fetchEntitysCountImplement();
    }

    protected function fetchProductsCount()
    {
        if ('product' !== $this->type) {
            throw new \Exception(__METHOD__ . " found exception! {$this->type} givend!");
        }

        return $this->fetchEntitysCountImplement();
    }

    protected function fetchCampaignsCount()
    {
        if ('campaign' !== $this->type) {
            throw new \Exception(__METHOD__ . " found exception! {$this->type} givend!");
        }

        return $this->fetchEntitysCountImplement('Cti');
    }

    protected function fetchCTIRecordsCount()
    {
        if ('ctirecord' !== $this->type) {
            throw new \Exception(__METHOD__ . " found exception! {$this->type} givend!");
        }

        return $this->fetchEntitysCountImplement('Cti');
    }

    /**
     * Gets the value of mdtTime.
     *
     * @return mixed
     */
    public function getMdtTime()
    {
        return $this->mdtTime;
    }

    /**
     * Sets the value of mdtTime.
     *
     * @param mixed $mdtTime the mdt time
     *
     * @return self
     */
    protected function setMdtTime($mdtTime)
    {
        $this->mdtTime = $mdtTime;

        return $this;
    }
}