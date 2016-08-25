<?php

namespace App\Export\FV\Sync;

use App\Export\FV\FVExport;
use Carbon\Carbon;

abstract class FVSyncExport extends FVExport
{
    /**
     * When there is exception occured, 
     * those in $exceptionObserver will be notified with mail
     * 
     * @var array
     */
    protected $exceptionObserver = [];
    protected $queId;

    /**
     * Gets the value of exceptionObserver.
     *
     * @return mixed
     */
    public function getExceptionObserver()
    {
        return $this->exceptionObserver;
    }

    /**
     * Sets the value of exceptionObserver.
     *
     * @param mixed $exceptionObserver the exception observer
     *
     * @return self
     */
    protected function setExceptionObserver($exceptionObserver)
    {
        $this->exceptionObserver = $exceptionObserver;

        return $this;
    }

    /**
     * The fetch start date, must override this constant
     */
    public function getStartDate()
    {
        return env('FVSYNC_STARTDATE', Carbon::now()->subMonth()->format('Y-m-d'));
    }

    abstract public function getPathEnv();

    /**
     * Gets the value of queId.
     *
     * @return mixed
     */
    public function getQueId()
    {
        return $this->queId;
    }

    /**
     * Sets the value of queId.
     *
     * @param mixed $queId the que id
     *
     * @return self
     */
    public function setQueId($queId)
    {
        $this->queId = $queId;

        return $this;
    }
}