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

    protected function fetchMembers($i)
    {
        $sql = str_replace(
            ['$mrtTime', '$begin', '$end'], 
            [$this->mdtTime->format('Y-m-d H:i:s'), $i, $i + $this->getChunkSize()], 
            Processor::getStorageSql('FV/Sync/member.sql')
        );

        return Processor::getArrayResult($sql);
    }

    protected function fetchOrders($i){}
    protected function fetchProducts($i){}
    protected function fetchCampaigns($i){}
    protected function fetchCTIRecords($i){}
    protected function fetchMembersCount()
    {
        $sql = str_replace(
            ['$mrtTime'], 
            [$this->mdtTime->format('Y-m-d H:i:s')], 
            Processor::getStorageSql('FV/Sync/member_count.sql')
        );

        return array_get(Processor::getArrayResult($sql), 0)['_count'];
    }

    protected function fetchOrdersCount(){}
    protected function fetchProductsCount(){}
    protected function fetchCampaignsCount(){}
    protected function fetchCTIRecordsCount(){}

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