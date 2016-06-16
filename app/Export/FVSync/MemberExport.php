<?php

namespace App\Export\FVSync;

use Carbon\Carbon;

class MemberExport extends \Maatwebsite\Excel\Files\NewExcelFile
{
    protected $max;
    protected $count;
    protected $info;
    protected $lastMrtTime;
    protected $chunkSize;
    protected $isBig5;

    public function getFilename()
    {   
        return 'Member_' . time();
    }

    public function getLastMrtTime()
    {
        return $this->lastMrtTime;
    }

    /**
     * Sets the value of lastMrtTime.
     *
     * @param mixed $lastMrtTime the last mrt time
     *
     * @return self
     */
    public function setLastMrtTime($lastMrtTime)
    {
        $this->lastMrtTime = $lastMrtTime;

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
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
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
     * Gets the value of isBig5.
     *
     * @return mixed
     */
    public function getIsBig5()
    {
        return $this->isBig5;
    }

    /**
     * Sets the value of isBig5.
     *
     * @param mixed $isBig5 the is big5
     *
     * @return self
     */
    public function setIsBig5($isBig5)
    {
        $this->isBig5 = $isBig5;

        return $this;
    }
}