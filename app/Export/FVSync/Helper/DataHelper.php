<?php

namespace App\Export\FVSync\Helper;

use App\Utility\Chinghwa\Database\Query\Processors\Processor;

/**
 * Help ExportHandler deal with data from flap, cti
 */
class DataHelper
{
    protected $chunkSize;
    protected $type;
    protected $count;
    protected $mdtTime;
    protected $map = [
        'member'    => 'Members', 
        'order'     => 'Orders', 
        'campaign'  => 'Campaigns', 
        'ctirecord' => 'CTIRecords', 
        'product'   => 'Products'
    ]; 

    public function __construct($type, $mdtTime, $chunkSize)
    {
        $this->setType($type)->setMdtTime($mdtTime)->setChunkSize($chunkSize)->initCount($mdtTime);
    }

    protected function initCount($mdtTime)
    {
        $this->setCount($this->fetchCount());
    }

    public function fetchCount()
    {
        return call_user_func(array($this, 'fetch' .  array_get($this->map, $this->type) . 'Count'));
    }

    public function fetchEntitys($export, $i)
    {
        return call_user_func([$this, 'fetch' .  array_get($this->map, $this->type)], $i);
    }

    /**
     * Gets the value of count.
     *
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Sets the value of count.
     *
     * @param mixed $count the count
     *
     * @return self
     */
    protected function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    protected function fetchMembers($i)
    {
        $sql = str_replace(
            ['$mrtTime', '$begin', '$end'], 
            [$this->mdtTime->format('Y-m-d H:i:s'), $i, $i + $this->getChunkSize()], 
            Processor::getStorageSql('/FVSync/member.sql')
        );

        return Processor::getArrayResult($sql);
    }

    protected function fetchOrders($i)
    {
        
    }

    protected function fetchProducts($i)
    {
        
    }

    protected function fetchCampaigns($i)
    {
        
    }

    protected function fetchCTIRecords($i)
    {
        
    }

    protected function fetchMembersCount()
    {
        return array_get(Processor::getArrayResult("SELECT COUNT(*) AS _count FROM POS_Member WITH(NOLOCK) WHERE LastModifiedDate >= '{$this->mdtTime->format('Y-m-d H:i:s')}'"), 0)['_count'];
    }

    protected function getOrdersCount($export)
    {
    }

    protected function getProductsCount($export)
    {
    }

    protected function getCampaignsCount($export)
    {
    }

    protected function getCTIRecordsCount($export)
    {
    }

    /**
     * Gets the value of type.
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the value of type.
     *
     * @param mixed $type the type
     *
     * @return self
     */
    protected function setType($type)
    {
        $this->type = $type;

        return $this;
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

    /**
     * Gets the value of map.
     *
     * @return mixed
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * Gets the value of chunkSize.
     *
     * @return mixed
     */
    public function getChunkSize()
    {
        return $this->chunkSize;
    }

    /**
     * Sets the value of chunkSize.
     *
     * @param mixed $chunkSize the chunk size
     *
     * @return self
     */
    protected function setChunkSize($chunkSize)
    {
        $this->chunkSize = $chunkSize;

        return $this;
    }
}