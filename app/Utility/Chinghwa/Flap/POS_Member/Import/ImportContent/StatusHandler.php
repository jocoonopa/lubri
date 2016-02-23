<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import\ImportContent;

use App\Model\Flap\PosMemberImportTaskContent;
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
 * 001000000: 有預產期
 * 010000000: 有醫院
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
        return $this
            ->initStatus()
            ->handleAddress()
            ->handleState()
            ->handleCellphone()
            ->handleHometel()
            ->handleIsPushed()
            ->handleBirthday()
            ->handlePeriodAt()
            ->handleHospital()
            ->handleEmail()
        ;
    }

    protected function initStatus()
    {
        $this->request->setStatus(0);

        return $this;
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

    /**
     * 000000001: 有地址
     * 
     * @return $this
     */
    protected function handleAddress()
    {
        if (NULL !== $this->content->homeaddress) {
            $this->request->setStatus(bindec('000000001'));
        }

        return $this;
    }

    protected function handleState()
    {
        if (NULL !== $this->content->state) {
            $this->request->setStatus(bindec('000000010'));
        }

        return $this;
    }

    protected function handleCellphone()
    {
        if (NULL !== $this->content->cellphone) {
            $this->request->setStatus(bindec('000000100'));
        }

        return $this;
    }

    protected function handleHometel()
    {
        if (NULL !== $this->content->hometel) {
            $this->request->setStatus(bindec('000001000'));
        }

        return $this;
    }

    protected function handleBirthday()
    {
        if (NULL !== $this->content->birthday) {
            $this->request->setStatus(bindec('000010000'));
        }

        return $this;
    }

    protected function handleIsPushed()
    {
        if (NULL !== $this->content->pushed_at) {
            $this->request->setStatus(bindec('000100000'));
        }

        return $this;
    }    

    protected function handlePeriodAt()
    {
        if (NULL !== $this->content->period_at) {
            $this->request->setStatus(bindec('001000000'));
        }

        return $this;
    }

    protected function handleHospital()
    {
        if (NULL !== $this->content->hospital) {
            $this->request->setStatus(bindec('010000000'));
        }

        return $this;
    }

    protected function handleEmail()
    {
        if (NULL !== $this->content->email) {
            $this->request->setStatus(bindec('100000000'));
        }

        return $this;
    }

    /**
     * Sets the value of content.
     *
     * @param mixed $content the content
     *
     * @return self
     */
    protected function setContent(PosMemberImportTaskContent $content)
    {
        $this->content = $content;

        return $this;
    }
}