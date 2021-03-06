<?php

namespace App\Export\FV\Import\Helper;

use App\Export\FV\Helper\DataHelper AS DH;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\Helper\Flap\PosMember\MemberCode;
use Carbon\Carbon;
use Log;

/**
 * Help ExportHandler deal with data from flap, cti
 */
class DataHelper extends DH
{
    protected $condition;

    public function __construct($export)
    {
        $type      = $export->getType();
        $condition = $export->getCondition();
        $limit     = $export->getLimit();
        $chunkSize = $export->getChunkSize();

        $this->setType($type)->setCondition($condition)->initCount()->setChunkSize($chunkSize > $limit ? $limit : $chunkSize);
    }

    protected function fetchMembers($i)
    {
        $lowerSerno = MemberCode::genSerNoStr($this->getCondition()['serno']);
        $upperSerno = MemberCode::genSerNoStr($this->getCondition()['upserno']);

        $whereStr = "WHERE POS_Member.SerNo >= '{$lowerSerno}' AND POS_Member.SerNo <= '{$upperSerno}'";

        $sql = str_replace(
            ['$whereCondition', '$begin', '$end'], 
            [$whereStr, $i, $i + $this->getChunkSize()], 
            Processor::getStorageSql('FV/Import/member.sql')
        );

        return Processor::getArrayResult($sql);
    }

    protected function fetchMembersCount()
    {
        $lowerSerno = MemberCode::genSerNoStr($this->getCondition()['serno']);
        $upperSerno = MemberCode::genSerNoStr($this->getCondition()['upserno']);

        return array_get(Processor::getArrayResult("SELECT COUNT(*) AS _count FROM POS_Member WITH(NOLOCK) WHERE POS_Member.SerNo >= '{$lowerSerno}' AND POS_Member.SerNo <= '{$upperSerno}'"), 0)['_count'];
    }

    protected function fetchOrders($i)
    {
        $placeholder = ['$serno', '$upserno', '$crtTime', '$begin', '$end'];
        $replace = [$this->getCondition()['serno'], $this->getCondition()['upserno'], $this->getCondition()['crttime'], $i, $i + $this->getChunkSize()];

        $sql = str_replace($placeholder, $replace, Processor::getStorageSql('FV/Import/order.sql'));

        return Processor::getArrayResult($sql);
    }

    protected function fetchProducts($i)
    {
        $sql = str_replace(
            ['$begin', '$end'], 
            [$i, $i + $this->getChunkSize()], 
            Processor::getStorageSql('FV/Import/product.sql')
        );

        return Processor::getArrayResult($sql);
    }

    protected function fetchProductsCount()
    {
        return array_get(Processor::getArrayResult("SELECT COUNT(*) AS _count FROM PIS_Goods WITH(NOLOCK) WHERE PIS_Goods.IsStop=0"), 0)['_count'];
    }

    protected function fetchCampaigns($i)
    {
        $sql = str_replace(
            ['$yesterday', '$tomorrow', '$begin', '$end'], 
            [Carbon::now()->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s'), $i, $i + $this->getChunkSize()], 
            Processor::getStorageSql('FV/Import/campaign.sql')
        );

        return Processor::getArrayResult($sql, Processor::DB_CTI);
    }
    
    protected function fetchOrdersCount()
    {
        $placeholder = ['$serno', '$upserno', '$crtTime'];
        $replace = [$this->getCondition()['serno'], $this->getCondition()['upserno'], $this->getCondition()['crttime']];

        $sql = str_replace($placeholder, $replace, Processor::getStorageSql('FV/Import/order_count.sql'));

        return array_get(Processor::getArrayResult($sql), 0)['_count'];
    }

    protected function fetchCampaignsCount()
    {
        $sql = str_replace(
            ['$yesterday', '$tomorrow'], 
            [Carbon::now()->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')], 
            Processor::getStorageSql('FV/Import/campaign_count.sql')
        );

        return array_get(Processor::getArrayResult($sql, Processor::DB_CTI), 0)['_count'];
    }

    /**
     * implements in CallDataHelper
     */
    protected function fetchLists($i){}

    /**
     * implements in CallDataHelper
     */
    protected function fetchListsCount(){}

    /**
     * implements in CallDataHelper
     */
    protected function fetchCalllogs($i){}

    /**
     * implements in CallDataHelper
     */
    protected function fetchCalllogsCount(){}


    /**
     * Gets the value of condition.
     *
     * @return mixed
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Sets the value of condition.
     *
     * @param mixed $condition the condition
     *
     * @return self
     */
    protected function setCondition($condition)
    {
        $this->condition = $condition;

        return $this;
    }
}