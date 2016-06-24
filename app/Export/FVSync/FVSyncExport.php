<?php

namespace App\Export\FVSync;

use Carbon\Carbon;

class FVSyncExport extends \Maatwebsite\Excel\Files\NewExcelFile
{
    protected $chunkSize;
    protected $commend;
    protected $output;
    protected $info;
    protected $exceptionObserver = [
        'selfindex@chinghwa.com.tw'  => 'Van',
        'john.cheung@vigasia.com.tw' => 'John',
        'jocoonopa@chinghwa.com.tw'  => '小洪'
    ];

    /**
     * Must be overrided
     */
    public function getMould(){}

    public function getFilename()
    {
        return __CLASS__;
    }

    /**
     * Decide which type of que would be use, must override this constant
     */
    public function getQueType()
    {
        return 'SomeTypeYouMustOverride';
    }

    /**
     * The fetch start date, must override this constant
     */
    public function getStartDate()
    {
        return '2222-12-31 23:59:59';
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
    public function setChunkSize($chunkSize)
    {
        $this->chunkSize = $chunkSize;

        return $this;
    }

    /**
     * Gets the value of commend.
     *
     * @return mixed
     */
    public function getCommend()
    {
        return $this->commend;
    }

    /**
     * Sets the value of commend.
     *
     * @param mixed $commend the commend
     *
     * @return self
     */
    public function setCommend($commend)
    {
        $this->commend = $commend;

        return $this;
    }

    /**
     * Gets the value of output.
     *
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Sets the value of output.
     *
     * @param mixed $output the output
     *
     * @return self
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Gets the value of info.
     *
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Sets the value of info.
     *
     * @param mixed $info the info
     *
     * @return self
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

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
}