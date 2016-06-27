<?php

namespace App\Export\FV\Sync;

use App\Export\FV\FVExport;

abstract class FVSyncExport extends FVExport
{
    protected $exceptionObserver = [];

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
    abstract public function getStartDate();
}