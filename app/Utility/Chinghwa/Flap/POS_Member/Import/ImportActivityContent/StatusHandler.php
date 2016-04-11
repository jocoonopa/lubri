<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import\ImportActivityContent;

use App\Model\Flap\PosMemberImportActivityTaskContent;
use App\Utility\Chinghwa\Flap\POS_Member\Import\ImportContent\StatusRequest;

/**
 * 狀態定義:
 *
 * 000000001: 有地址
 * 000000010: 有對應到的區
 * 000000100: 有手機
 * 000001000: 有住家電話
 * 000010000: 有生日
 * 000100000: 有成功推送
 * 100000000: 有Email
 */
class StatusHandler
{
    protected $request;
    protected $content;

    public function __construct(StatusRequest $request)
    {
        $this->setRequest($request)->setContent($request->getContent())->handleRequest();
    }

    public function handleRequest()
    {
        
    }

    protected function initStatus()
    {
       
    }

    /**
     * Gets the value of request.
     *
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets the value of request.
     *
     * @param mixed $request the request
     *
     * @return self
     */
    protected function setRequest(StatusRequest $request)
    {
        $this->request = $request;

        return $this;
    }    
}