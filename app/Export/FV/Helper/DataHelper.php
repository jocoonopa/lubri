<?php

namespace App\Export\FV\Helper;

use App\Utility\Chinghwa\Database\Query\Processors\Processor;

/**
 * Help ExportHandler deal with data from flap, cti
 */
abstract class DataHelper
{
    protected $chunkSize;
    protected $type;
    protected $count;
    protected $map = [
        'member'    => 'Members', 
        'order'     => 'Orders', 
        'campaign'  => 'Campaigns', 
        'list'      => 'Lists', 
        'calllog'   => 'Calllogs',
        'product'   => 'Products'
    ]; 

    abstract protected function fetchMembers($i);
    abstract protected function fetchOrders($i);
    abstract protected function fetchProducts($i);
    abstract protected function fetchCampaigns($i);
    abstract protected function fetchCalllogs($i);
    abstract protected function fetchLists($i);
    abstract protected function fetchMembersCount();
    abstract protected function fetchOrdersCount();
    abstract protected function fetchProductsCount();
    abstract protected function fetchCampaignsCount();
    abstract protected function fetchCalllogsCount();
    abstract protected function fetchListsCount();

    protected function initCount()
    {
        return $this->setCount($this->fetchCount());
    }

    public function fetchCount()
    {
        return call_user_func(array($this, 'fetch' .  array_get($this->map, $this->type) . 'Count'));
    }

    public function fetchEntitys($i)
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