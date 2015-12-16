<?php

namespace spec\App\Utility\Chinghwa\Flap\CCS_OrderIndex;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PrefixHandlerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('App\Utility\Chinghwa\Flap\CCS_OrderIndex\PrefixHandler');
    }

    function it_should_gen_update_ccs_orderdivindex_query()
    {
    	$nos = ['CT201512160046-1', 'CT201512160047-1', 'CT201512160048-1'];

    	$this->genUpdateCCSOrderDivIndexQuery($nos)->shouldHaveCount(count($nos));
    	$this->genUpdateCCSOrderDivIndexQuery($nos)->shouldContain("UPDATE CCS_OrderDivIndex SET No='CT201512160046-1' WHERE No='CT201512160046-1'");
    	$this->genUpdateCCSOrderDivIndexQuery($nos)->shouldContain("UPDATE CCS_OrderDivIndex SET No='CT201512160047-1' WHERE No='CT201512160047-1'");
    	$this->genUpdateCCSOrderDivIndexQuery($nos)->shouldContain("UPDATE CCS_OrderDivIndex SET No='CT201512160048-1' WHERE No='CT201512160048-1'");
    }

    function it_should_gen_update_ccs_orderindexquerys_by_iterate_order_nos()
    {
    	$nos = ['CT201512160046', 'CT201512160047', 'CT201512160048'];

    	$this->genUpdateCCSOrderIndexQuerysByIterateOrderNos($nos)->shouldHaveCount(count($nos));
    	$this->genUpdateCCSOrderIndexQuerysByIterateOrderNos($nos)->shouldContain("UPDATE CCS_OrderIndex SET OrderNo='CT201512160046' WHERE OrderNo='CT201512160046'");
    	$this->genUpdateCCSOrderIndexQuerysByIterateOrderNos($nos)->shouldContain("UPDATE CCS_OrderIndex SET OrderNo='CT201512160047' WHERE OrderNo='CT201512160047'");
    	$this->genUpdateCCSOrderIndexQuerysByIterateOrderNos($nos)->shouldContain("UPDATE CCS_OrderIndex SET OrderNo='CT201512160048' WHERE OrderNo='CT201512160048'");
    }
}
